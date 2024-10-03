<?php

namespace frontend\controllers;

use common\models\Batch;
use common\models\File;
use common\models\Payment;
use common\models\PaymentSearch;
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
 * PaymentController implements the CRUD actions for Payment model.
 */
class PaymentController extends Controller
{
    const DIR_ACT = 'protected/uploads/payment/acts/';                // путь для хранения файлов исследований
    const DIR_ACT_CLIENT = 'protected/uploads/payment/clients_acts/'; // путь для хранения файлов исследований
    const DIR_PAY = 'protected/uploads/payment/pay_docs/';            // путь для хранения файлов исследований
    const DIR_INVOICE = 'protected/uploads/payment/invoices/';        // путь для хранения файлов исследований

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
                            'roles' => ['payment/see']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create'],
                            'roles' => ['payment/create']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['update'],
                            'roles' => ['payment/update']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['delete', 'lock', 'delete-file'],
                            'roles' => ['payment/delete']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['upload-client-act', 'delete-client-file'],
                            'roles' => ['client']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['unlock'],
                            'roles' => ['admin']
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * Lists all Payment models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $subqueries = Batch::find()
            ->select('batch.id')
            ->joinWith('services')
            ->joinWith('samples')
            ->andWhere(['or',
                ['service.locked' => 0],
                ['sample.busy' => 0]
            ])
            ->groupBy('batch.id');

        $completedBatches = Batch::find()
            ->where(['IS', 'payment_id', NULL]);

        if ($subqueries->count() > 0) {
            $completedBatches->andWhere(['NOT IN', 'id', $subqueries]);
        }

        $alertBatchProvider = new ActiveDataProvider([
            'query' => $completedBatches,
            'pagination' => ['pageSize' => 5]
        ]);

        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'alertBatchProvider' => $alertBatchProvider
        ]);
    }

    /**
     * Displays a single Payment model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $servicesSearchModel = new ServiceSearch();
        $servicesDataProvider = $servicesSearchModel->search(Yii::$app->request->queryParams, batch_id: $model->batch->id);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'servicesDataProvider' => $servicesDataProvider
        ]);
    }

    /**
     * Creates a new Payment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($batch_id)
    {
        $batch = Batch::findOne($batch_id);
        $model = new Payment();

        $services = $batch->services;
        $fact_sum = 0;
        foreach ($services as $service) {
            $fact_sum += $service->pre_sum;
        }
        $model->fact_sum = $fact_sum;

        if ($this->request->isPost) {
            $this->uploadFile(
                $model,
                'uploadedFileAct',
                'fileAct',
                self::DIR_ACT,
                'file_act',
                $batch->contract->organization_id
            );
            $this->uploadFile(
                $model,
                'uploadedFilePay',
                'filePay',
                self::DIR_PAY,
                'file_pay',
                $batch->contract->organization_id
            );
            $this->uploadFile(
                $model,
                'uploadedFileInvoice',
                'fileInvoice',
                self::DIR_INVOICE,
                'file_invoice',
                $batch->contract->organization_id
            );

            if ($model->load($this->request->post()) && $model->save()) {
                $batch->payment_id = $model->id;
                $batch->save(false);

                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Payment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->locked) {
            throw new ForbiddenHttpException(Yii::t('app', 'Изменение запрещено.'));
        }
        if ($this->request->isPost) {
            $this->uploadFile(
                $model,
                'uploadedFileAct',
                'fileAct',
                self::DIR_ACT,
                'file_act',
                $model->batch->contract->organization_id
            );
            $this->uploadFile(
                $model,
                'uploadedFilePay',
                'filePay',
                self::DIR_PAY,
                'file_pay',
                $model->batch->contract->organization_id
            );
            $this->uploadFile(
                $model,
                'uploadedFileInvoice',
                'fileInvoice',
                self::DIR_INVOICE,
                'file_invoice',
                $model->batch->contract->organization_id
            );

            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Payment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->locked) {
            throw new ForbiddenHttpException(Yii::t('app', 'Изменение запрещено.'));
        }
        try {
            $model->delete();
            Yii::$app->session->setFlash('success', 'Акт оплаты успешно удален.');
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
     * Finds the Payment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Payment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Payment::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Метод для работы с прикрепленными файлами
     * 
     * @param \common\models\Payment $model экземпляр payment
     * @param string $instance наиманование атрибута с файлом из формы
     * @param string $relation наименование связи сущностей (fileAct/filePay/...)
     * @param string $dir путь для хранения файла
     * @param string $property наименование атрибута, в который записывать индекс файлами
     * @param int $org_id индекс организации
     * @return bool
     */
    protected function UploadFile($model, $instance, $relation, $dir, $property, $org_id)
    {
        $file = UploadedFile::getInstance($model, $instance);
        if ($file) {
            if ($model->$relation) {
                unlink($model->$relation->filepath);
                $model->$relation->delete();
            }

            $filename = time() . '.' . $file->extension;
            $file->saveAs(Yii::$app->basePath . '/' . $dir . $filename);

            $fileModel = new File();
            $fileModel->filepath = $dir . $filename;
            $fileModel->filename = $file->name;
            $fileModel->filesize = $file->size;
            $fileModel->organization_id = $org_id;
            $fileModel->save();

            $model->$property = $fileModel->id;
            return true;
        }
        return false;
    }

    /**
     * Действие для формы с прикреплением подписанного акта для клиента
     * 
     * @param int $id индекс оплаты
     * @return string|\yii\web\Response
     * @throws ForbiddenHttpException если пользователь не принадлежит этой организации
     */
    public function actionUploadClientAct($id)
    {
        $model = $this->findModel($id);
        $user_org_id = Yii::$app->user->identity->organization_id;
        if ($model->batch->contract->organization_id == $user_org_id) {
            if ($model->locked) {
                throw new ForbiddenHttpException(Yii::t('app', 'Изменение запрещено.'));
            }
            if ($this->request->isPost) {
                $this->UploadFile(
                    $model,
                    'uploadedFileActClient',
                    'fileActClient',
                    self::DIR_ACT_CLIENT,
                    'file_act_client',
                    $user_org_id
                );
                if ($model->load($this->request->post()) && $model->save()) {
                    return $this->redirect(['client/index', 'org_id' => $user_org_id]);
                }
            }

            return $this->render('upload_client_act', [
                'model' => $model,
            ]);
        }
        throw new ForbiddenHttpException(Yii::t('app', 'Доступ ограничен.'));
    }

    /**
     * Действие на закрепление акта для блокировки редактирования
     * 
     * @param int $id индекс оплаты
     * @return string|yii\web\Response
     * @throws ForbiddenHttpException не хватает прав
     */
    public function actionLock($id)
    {
        $model = $this->findModel($id);
        if ($model->checkSetAllValues()) {
            $model->locked = 1;
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Акт оплаты успешно закреплен.');
            }
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Действие на разблокировку акта
     * 
     * @param int $id индекс оплаты
     * @return string|yii\web\Response
     * @throws ForbiddenHttpException не хватает прав
     */
    public function actionUnlock($id)
    {
        $model = $this->findModel($id);
        if ($model->locked == 1) {
            $model->locked = 0;
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Акт оплаты успешно разблокирован.');
            }
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    /** 
     * Действие на удаление файла
     * 
     * @param int $id индекс payment
     * @param string $file наименование атрибута модели
     * @return string|yii\web\Response
     * @throws ForbiddenHttpException не хватает прав | акт закреплен
     */
    public function actionDeleteFile($id, $file)
    {
        $model = $this->findModel($id);
        if ($model->locked) {
            throw new ForbiddenHttpException(Yii::t('app', 'Изменение запрещено.'));
        }
        if (unlink($model->$file->getPathLink())) {
            $model->$file->delete();
        }
        return $this->redirect(['update', 'id' => $id]);
    }


    /** 
     * Действие на удаление файла у клиента
     * 
     * @param int $id индекс payment
     * @return string|yii\web\Response
     * @throws ForbiddenHttpException не хватает прав | акт закреплен
     */
    public function actionDeleteClientFile($id)
    {
        $model = $this->findModel($id);
        if ($model->locked) {
            throw new ForbiddenHttpException(Yii::t('app', 'Изменение запрещено.'));
        }
        if (unlink($model->fileActClient->getPathLink())) {
            $model->fileActClient->delete();
        }
        return $this->redirect(['upload-client-act', 'id' => $id]);
    }
}
