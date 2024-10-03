<?php

namespace frontend\controllers;

use common\models\Departament;
use common\models\DepartamentSearch;
use common\models\JobSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DepartamentController implements the CRUD actions for Departament model.
 */
class DepartamentController extends Controller
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
                            'roles' => ['departament/see']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create'],
                            'roles' => ['departament/create']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['update'],
                            'roles' => ['departament/update']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['delete'],
                            'roles' => ['departament/delete']
                        ],
                    ]
                ]
            ]
        );
    }

    /**
     * Lists all Departament models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DepartamentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Departament model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $jobSearchModel = new JobSearch();
        $jobDataProvider = $jobSearchModel->search(Yii::$app->request->queryParams, departament_id: $id);

        return $this->render('view', [
            'jobSearchModel' => $jobSearchModel,
            'jobDataProvider' => $jobDataProvider,
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Departament model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Departament();

        if ($this->request->isPost) {
            if ($model->role != 'laboratory') {
                $model->short_name = null;
                $model->abbreviation = null;
            }
            if ($model->load($this->request->post()) && $model->save()) {
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
     * Updates an existing Departament model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost and $model->load($this->request->post())) {
            if ($model->role != 'laboratory') {
                $model->short_name = null;
                $model->abbreviation = null;
            }
            if ($id == 1 and $model->role != 'admin') {
                Yii::$app->session->setFlash('error', 'У этого отдела нельзя сменить роль пользователей!');
            } elseif ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Departament model.
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
            Yii::$app->session->setFlash('success', 'Отдел успешно удален.');
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
     * Finds the Departament model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Departament the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Departament::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
