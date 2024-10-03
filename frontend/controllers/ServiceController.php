<?php

namespace frontend\controllers;

use common\models\Batch;
use common\models\File;
use common\models\PriceList;
use common\models\Sample;
use common\models\SampleSearch;
use common\models\SampleService;
use common\models\Service;
use common\models\ServiceSearch;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ServiceController implements the CRUD actions for Service model.
 */
class ServiceController extends Controller
{
    const DIR = 'protected/uploads/service/results/'; // путь для хранения файлов исследований

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['index', 'view'],
                            'roles' => ['service/see']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create-empty', 'create-idle', 'create-bulk', 'multi-select', 'once-select'],
                            'roles' => ['service/create']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['upload-results', 'samples-edit'],
                            'roles' => ['service/update']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['delete', 'delete-file'],
                            'roles' => ['service/delete']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['index-late'],
                            'roles' => ['service/seeLate']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['lock'],
                            'roles' => ['admin']
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * Lists all Service models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $batch_idle_samples = Batch::find()
            ->joinWith('samples', false, 'INNER JOIN')
            ->where(['sample.busy' => 0])
            ->andWhere(['sample.losted_at' => null])
            ->andWhere(['sample.departament_id' => Yii::$app->user->identity->staff->job->departament_id])
            ->groupBy('batch_id')->orderBy('batch.id DESC');

        $alertSamplesProvider = new ActiveDataProvider([
            'query' => $batch_idle_samples,
            'pagination' => ['pageSize' => 5],
        ]);

        $searchModel = new ServiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'alertSamplesProvider' => $alertSamplesProvider
        ]);
    }

    /**
     * Displays a single Service model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $sampleSearchModel = new SampleSearch();
        $sampleDataProvider = $sampleSearchModel->search(Yii::$app->request->queryParams, service_id: $id);

        return $this->render('view', [
            'sampleSearchModel' => $sampleSearchModel,
            'sampleDataProvider' => $sampleDataProvider,
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Действие на создание пустого исследования без проб
     * @return string|\yii\web\Response
     */
    public function actionCreateEmpty()
    {
        $model = new Service(['scenario' => Service::SCENARIO_CREATE_EMPTY]);

        if ($this->request->isPost) {
            $research_id = Yii::$app->request->post('Service')['research_id'];
            $research = PriceList::findOne((int)$research_id);

            $this->initializeServiceModel($model, $research, 0, date('Y-m-d H:i:s'));

            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create_empty', [
            'model' => $model,
        ]);
    }


    /**
     * Инициализация атрибутов экземпляра Service
     * @param Service $model
     * @param PriceList $research
     * @param int $amount
     * @param string $date
     * @return void
     */
    private function initializeServiceModel($model, $research, $amount, $date)
    {
        $model->research = $research->research;
        $model->price = $research->price;
        $model->started_at = $date;
        $model->staff_id = Yii::$app->user->identity->staff_id;
        $model->pre_sum = $amount * $model->price;
        $model->predict_date = $model->predictTheDate($research->period);
    }

    
    /**
     * Действие по созданию исследования по уведомлению
     * @param int $batch_id
     * @return string|Yii\web\Response
     */
    public function actionCreateIdle($batch_id)
    {
        $batch = Batch::findOne($batch_id);
        $model = new Service(['scenario' => Service::SCENARIO_CREATE_IDLE]);
        $model->batch_id = $batch_id;

        if ($this->request->isPost) {
            return $this->processIdleService($model, $batch);
        }

        $model->loadDefaultValues();

        return $this->render('create_idle', [
            'model' => $model,
            'batch' => $batch
        ]);
    }


    /**
     * Формирование исследования из уведомления
     * @param Service $model
     * @param Batch $batch
     * @return Yii\web\Response
     */
    private function processIdleService($model, $batch)
    {
        $amount = (int)Yii::$app->request->post('Service')['amount'];
        $research_id = Yii::$app->request->post('Service')['research_id'];
        $research = PriceList::findOne((int)$research_id);

        $this->initializeServiceModel($model, $research, $amount, date('Y-m-d H:i:s'));

        if ($model->load($this->request->post()) && $model->save()) {
            if ($amount > (int)$batch->getIdleSamplesCount()) {
                Yii::$app->session->setFlash('error', 'При формировании состава проб для исследования произошла ошибка. Попробуйте снова, либо заполните вручную.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
            for ($i = 1; $i <= $amount; $i++) {
                $sample_service = new SampleService();
                $sample_service->service_id = $model->id;

                $sample = Sample::find()
                    ->where([
                        'batch_id' => $model->batch_id,
                        'losted_at' => null,
                        'departament_id' => Yii::$app->user->identity->staff->job->departament_id,
                        'busy' => 0
                    ])->orderBy(['id' => SORT_ASC])
                    ->one();
                
                if ($sample) {
                    $sample_service->sample_id = $sample->id;
                    $sample->busy = 1;
                    $sample->save(false);
                    $sample_service->save();
                }
            }
        }
        return $this->redirect(['view', 'id' => $model->id]);
    }


    /**
     * Действие с редиректом на множественное создание исследований для нескольких проб
     * @return Yii\web\Response
     */
    public function actionMultiSelect()
    {
        $selectedSamples = Yii::$app->request->post('selection_samples');

        if ($selectedSamples) {
            $selectedSamples = implode(',', $selectedSamples);
            return $this->redirect(['create-bulk', 'ids' => $selectedSamples]);
        } else {
            Yii::$app->session->setFlash('info', 'Сначала выберите пробы.');
            return $this->redirect(Yii::$app->request->referrer);
        }
    }


    /**
     * Действие с редиректом на множественное создание исследований для одной пробы
     * @param int $id
     * @return Yii\web\Response
     */
    public function actionOnceSelect($id)
    {
        if (!empty($id)) {
            return $this->redirect(['create-bulk', 'ids' => $id]);
        }
        Yii::$app->session->setFlash('info', 'Сначала выберите пробу.');
        return $this->redirect(Yii::$app->request->referrer);
    }


    /**
     * Действие по регистрации нескольких исследований для выбранных проб
     * @param string $ids id проб
     * @return string|Yii\web\Response
     */
    public function actionCreateBulk($ids)
    {
        $ids = explode(',', $ids);

        if (empty($ids)) {
            Yii::$app->session->setFlash('info', 'Сначала выберите пробы.');
            return $this->redirect(Yii::$app->request->referrer);
        }

        $sample = Sample::findOne($ids[0]);
        if (!$sample) {
            Yii::$app->session->setFlash('error', 'Некоторые пробы не найдены.');
            return $this->redirect(['service/index']);
        }

        $batch = $sample->batch;
        if ($this->request->isPost && $researches = Yii::$app->request->post('Batch')['researches']) {
            return $this->processResearches($researches, $ids, $batch);
        }

        return $this->render('create_bulk', [
            'ids' => $ids,
            'batch' => $batch,
        ]);
    }


    /**
     * Формирование исследований
     * @param array $researches
     * @param mixed $array
     * @param Batch $batch
     * @return Yii\web\Response
     */
    private function processResearches($researches, $ids, $batch)
    {
        if (!$researches) {
            Yii::$app->session->setFlash('info', 'Сначала выберите исследования.');
            return $this->redirect(Yii::$app->request->referrer);
        }

        $start_date = date('Y-m-d H:i:s');

        foreach ($researches as $research_id) {
            $model = new Service();
            $model->sample_ids = $ids;
            $model->batch_id = $batch->id;
            $research = PriceList::findOne($research_id);
            $this->initializeServiceModel($model, $research, count($ids), $start_date);

            if (!$batch->load($this->request->post()) || !$model->save()) {
                return $this->redirect(['service/index']);
            }

            foreach ($ids as $sample_id) {
                $sample_service = new SampleService();
                $sample_service->service_id = $model->id;
                $sample_service->sample_id = $sample_id;
                $sample = Sample::findOne($sample_id);
                $sample->busy = 1;
                $sample->save(false);
                $sample_service->save();
            };
        }

        return $this->redirect(['index', 'ServiceSearch[started_at]' => $start_date]);
    }


    /**
     * Updates an existing Service model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUploadResults($id)
    {
        $model = $this->findModel($id);

        if ($model->batch->payment and $model->batch->payment->locked) {
            throw new ForbiddenHttpException(Yii::t('app', 'Изменение запрещено.'));
        }
        if ($model->getActiveSamples()->count() < 1) {
            Yii::$app->session->setFlash('error', 'Нельзя закрыть исследование без активных проб!');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        if ($this->request->isPost) {
            $file = UploadedFile::getInstance($model, 'uploadedFile');
            if ($file) {
                if ($model->file) {
                    unlink($model->file->filepath);
                    $model->file->delete();
                }

                $filename = time() . '.' . $file->extension;
                $file->saveAs(Yii::$app->basePath . '/' . self::DIR . $filename);

                $fileModel = new File();
                $fileModel->filepath = self::DIR . $filename;
                $fileModel->filename = $file->name;
                $fileModel->filesize = $file->size;
                $fileModel->organization_id = $model->batch->contract->organization_id;
                $fileModel->departament_id = $model->staff->job->departament_id;
                $fileModel->save();

                $model->file_id = $fileModel->id;
                $model->pre_sum = $model->price * $model->getActiveSamples()->count();

                if (empty($model->completed_at)) {
                    $model->completed_at = date('Y-m-d H:i:s');
                    if ($model->getActiveSamples()->count() == $model->getSampleServices()->count()) {
                        $model->locked = 1;
                    }
                }
            }
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Действие на изменение состава проб для исследования
     * @param $id индекс исследования
     */
    public function actionSamplesEdit($id)
    {
        $model = $this->findModel($id);
        if ($model->batch->payment and $model->batch->payment->locked) {
            throw new ForbiddenHttpException(Yii::t('app', 'Изменение запрещено.'));
        }
        $searchModel = new SampleSearch();
        $departament_id = $model->staff->job->departament_id;

        $samplesServiceProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            departament_id: $departament_id,
            batch_id: $model->batch_id,
            service_id: $id
        );
        $samplesBatchProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            departament_id: $departament_id,
            batch_id: $model->batch_id,
            un_service_id: $id
        );

        $success = true;
        if ($this->request->isPost) {
            $selection_in = Yii::$app->request->post('selection_in');
            if (!empty($selection_in)) {
                foreach ($selection_in as $item_in) {
                    $sample = Sample::findOne($item_in);

                    $relation_item = SampleService::findOne([
                        'sample_id' => $sample->id,
                        'service_id' => $id
                    ]);
                    if (!$relation_item->delete()) {
                        $success = false;
                    }
                    if (!SampleService::findOne(['sample_id' => $sample->id])) {
                        $sample->busy = 0;
                        $sample->save(false);
                    }
                }
            }

            $selection_out = Yii::$app->request->post('selection_out');
            if (!empty($selection_out)) {
                foreach ($selection_out as $item_out) {
                    $sample = Sample::findOne($item_out);

                    $relation_item = new SampleService();
                    $relation_item->sample_id = $sample->id;
                    $relation_item->service_id = $id;
                    if (!$relation_item->save()) {
                        $success = false;
                    }

                    if ($sample->busy == 0) {
                        $sample->busy = 1;
                        $sample->save(false);
                    }
                }
            }

            if ($success) {
                if (empty($selection_out) && empty($selection_in)) {
                    Yii::$app->session->setFlash('warning', 'Для проведения смены требуется сначала выбрать необходимые пробы!');
                } else {
                    if (!empty($selection_out)) $model->pre_sum += count($selection_out) * $model->price;
                    if (!empty($selection_in)) $model->pre_sum -= count($selection_in) * $model->price;

                    $model->save(false);
                    Yii::$app->session->setFlash('success', 'Изменение успешно!');
                }
                return $this->redirect(['samples-edit', 'id' => $id]);
            } else {
                Yii::$app->session->setFlash('error', 'Возникла ошибка. Пожалуйста проверьте данные.');
            }
        }

        return $this->render('samples_edit', [
            'service' => $model,
            'samplesBatchProvider' => $samplesBatchProvider,
            'samplesServiceProvider' => $samplesServiceProvider
        ]);
    }

    /**
     * Deletes an existing Service model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->batch->payment and $model->batch->payment->locked) {
            throw new ForbiddenHttpException(Yii::t('app', 'Изменение запрещено.'));
        }

        try {
            $model->delete();
            Yii::$app->session->setFlash('success', 'Исследование успешно удалено.');
            return $this->redirect(['index']);
        } catch (yii\db\Exception $e) {
            if ($e->errorInfo[1] == 1451) {
                Yii::$app->session->setFlash('error', 'Удаление невозможно. У этой записи есть связанные данные.');
            } else {
                Yii::$app->session->setFlash('error', 'Неизвестная ошибка при удалении.');
            }

            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * Finds the Service model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Service the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Service::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /** 
     * Действие на удаление файла
     * 
     * @param int $id индекс payment
     * @return string|yii\web\Response
     * @throws ForbiddenHttpException не хватает прав
     */
    public function actionDeleteFile($id)
    {
        $model = $this->findModel($id);
        if ($model->batch->payment && $model->batch->payment->locked) {
            throw new ForbiddenHttpException(Yii::t('app', 'Изменение запрещено.'));
        }
        if (unlink($model->file->getPathLink())) {
            $model->file->delete();
        }
        return $this->redirect(['upload-results', 'id' => $id]);
    }

    /**
     * Действие на ручное закрытие исследования
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionLock($id)
    {
        $model = $this->findModel($id);
        $model->locked = 1;
        $model->save(false);

        Yii::$app->session->setFlash('success', 'Завершение исследования успешно подтверждено.');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Страница с просроченными исследованиями
     * @return string
     */
    public function actionIndexLate()
    {
        $searchModel = new ServiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, late: true);

        return $this->render('index-late', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
