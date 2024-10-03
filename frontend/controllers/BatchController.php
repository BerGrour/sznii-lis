<?php

namespace frontend\controllers;

use common\models\Batch;
use common\models\BatchSearch;
use common\models\Departament;
use common\models\Sample;
use common\models\SampleSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;

/**
 * BatchController implements the CRUD actions for Batch model.
 */
class BatchController extends Controller
{
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
                    'only' => ['index', 'view', 'create', 'update', 'delete', 'bulk-delete'],
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['index', 'view'],
                            'roles' => ['batch/see']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create'],
                            'roles' => ['batch/create']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['update'],
                            'roles' => ['batch/update']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['delete', 'bulk-delete-samples'],
                            'roles' => ['batch/delete']
                        ],
                    ]
                ]
            ]
        );
    }

    /**
     * Lists all Batch models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BatchSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Batch model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $sampleSearchModel = new SampleSearch();
        $sampleDataProvider = $sampleSearchModel->search(Yii::$app->request->queryParams, batch_id: $id);

        return $this->render('view', [
            'sampleSearchModel' => $sampleSearchModel,
            'sampleDataProvider' => $sampleDataProvider,
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Batch model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $departaments = Departament::find()->where(['role' => 'laboratory'])->all();

        $model = new Batch();
        $model->employed_at = date('Y-m-d H:i:s');
        $model->staff_id = Yii::$app->user->identity->staff_id;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                $labs = Yii::$app->request->post('Batch')['labs_amount'];
                if ($model->bulkCreateSamples($labs, $model)) {
                    Yii::$app->session->setFlash('success', 'Партия проб успешно зарегестрирована.');
                } else {
                    Yii::$app->session->setFlash('error', 'Во время регистрации проб произошла ошибка!');
                }
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'departaments' => $departaments,
        ]);
    }

    /**
     * Updates an existing Batch model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Batch model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        try {
            $model->delete();
            Yii::$app->session->setFlash('success', 'Партия успешно удалена.');
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
     * Массовое удаление выбранных проб из партии
     * @return Yii\web\Response
     */
    public function actionBulkDeleteSamples()
    {
        $selectedIds = Yii::$app->request->post('selection_samples');
        $array_busy_samples = [];

        if ($selectedIds) {
            foreach ($selectedIds as $item) {
                $model = Sample::findOne($item);
                try {
                    $model->delete();
                } catch (yii\db\Exception $e) {
                    if ($e->errorInfo[1] == 1451) {
                        array_push($array_busy_samples, $model->identificator);
                    } else {
                        Yii::$app->session->setFlash('error', 'Неизвестная ошибка при удалении.');
                    }
                }
            }
            if (empty($array_busy_samples)) {
                Yii::$app->session->setFlash('success', 'Выбранные пробы успешно удалены.');
            } else {
                Yii::$app->session->setFlash('warning', 'Удаление произведено не полностью, так как есть связанные данные у проб:<br/>(' . implode('; ', $array_busy_samples) . ')');
            }
        } else {
            Yii::$app->session->setFlash('info', 'Сначала выберите пробы, которые требуется удалить.');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the Batch model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Batch the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Batch::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Метод для использования в виджете Select2, отбирающий ограниченное количество результатов
     * с поиском по дате-времени регистрации партии
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionList($term = null, $page = 1, $limit = 20, $empty = false)
    {
        if (Yii::$app->request->isAjax) {
            $out = ['more' => false, 'results' => []];
            $query = Batch::find();
            if ($empty) {
                $query->joinWith('samples', false, 'INNER JOIN')
                    ->where(['sample.departament_id' => Yii::$app->user->identity->staff->job->departament_id])
                    ->groupBy('batch.id');
            }
            $data = $query
                ->select([
                    'id' => '[[batch.id]]',
                    'text' => 'CONCAT([[batch.employed_at]], "\t(", COUNT([[sample.id]]), " шт.)")',
                ])
                ->andFilterWhere(['like', 'batch.employed_at', $term])
                ->orderBy(['batch.employed_at' => SORT_DESC])
                ->groupBy('batch.id')
                ->limit($limit + 1)
                ->offset(($page - 1) * $limit)
                ->asArray()
                ->all();
            if (count($data) === $limit + 1) {
                $out['more'] = true;
                array_pop($data);
            }
            $out['results'] = $data;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $out;
        }
        throw new ForbiddenHttpException;
    }
}
