<?php

namespace frontend\controllers;

use common\models\Sample;
use common\models\SampleSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SampleController implements the CRUD actions for Sample model.
 */
class SampleController extends Controller
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
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['index', 'view'],
                            'roles' => ['sample/see']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create'],
                            'roles' => ['sample/create']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['update'],
                            'roles' => ['sample/update']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['delete'],
                            'roles' => ['sample/lost']
                        ],
                    ]
                ]
            ]
        );
    }

    /**
     * Lists all Sample models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SampleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Sample model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Sample model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        // TODO: нужно ли делать создание одной пробы?
        $model = new Sample();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if ($model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Sample model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {
            $lost_date = Yii::$app->request->post('Sample')['lost_date'];
            $lost_time = Yii::$app->request->post('Sample')['lost_time'];

            if ($lost_date && $lost_time) {
                $model->losted_at = "{$lost_date} {$lost_time}:00";
            } elseif ($lost_date || $lost_time) {
                Yii::$app->session->setFlash('error', 'Если проба потеряна, то требуется указать полностью дату и время. Иначе не заполняйте поля с датой и временем.');
                return $this->render('update', [
                    'model' => $model,
                ]);
            } else $model->losted_at = null;

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        if ($model->losted_at) {
            $dateParts = explode(' ', $model->losted_at);
            $model->lost_date = $dateParts[0];
            $model->lost_time = $dateParts[1];
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Sample model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $batch_id = $model->batch_id;

        $message = "Проба {$model->identificator} отмечена как \"Потеряна.\"";

        if (Yii::$app->user->can('sample/delete')) {
            try {
                $model->delete();
                Yii::$app->session->setFlash('success', "Проба {$model->identificator} была удалена.");
                return $this->redirect(['/batch/view', 'id' => $batch_id]);
            } catch (yii\db\Exception $e) {
                if ($e->errorInfo[1] == 1451) {
                    $message = 'Удаление невозможно. У этой записи есть связанные данные. Вместо этого пробы была отмечена как: "Потеряна".';
                } else {
                    Yii::$app->session->setFlash('error', 'Неизвестная ошибка при удалении.');
                    return $this->redirect(Yii::$app->request->referrer);
                }
            }
        }
        if (Yii::$app->user->can('sample/lost')) {
            $model->losted_at = date('Y-m-d H:i:s');
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', $message);
            }
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the Sample model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Sample the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sample::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
