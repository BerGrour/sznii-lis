<?php

namespace frontend\controllers;

use common\models\Contract;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ContractController implements the CRUD actions for Contract model.
 */
class ContractController extends Controller
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
                            'actions' => ['view'],
                            'roles' => ['contract/see']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create'],
                            'roles' => ['contract/create']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['update'],
                            'roles' => ['contract/update']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['delete'],
                            'roles' => ['contract/delete']
                        ],
                    ]
                ]
            ]
        );
    }

    /**
     * Displays a single Contract model.
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
     * Creates a new Contract model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param int $organization_id индекс организации
     * @return string|\yii\web\Response
     */
    public function actionCreate($organization_id)
    {
        $model = new Contract();
        $model->organization_id = $organization_id;

        if ($this->request->isPost) {
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
     * Updates an existing Contract model.
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
     * Deletes an existing Contract model.
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
            Yii::$app->session->setFlash('success', 'Договор успешно удален.');
            return $this->redirect(['/organization/view', 'id' => $model->organization_id]);
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
     * Finds the Contract model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Contract the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Contract::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
