<?php

namespace frontend\controllers;

use common\models\ContractSearch;
use common\models\Organization;
use common\models\OrganizationSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * OrganizationController implements the CRUD actions for Organization model.
 */
class OrganizationController extends Controller
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
                    'only' => ['index', 'view', 'create', 'update', 'delete'],
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['index', 'view'],
                            'roles' => ['organization/see']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create'],
                            'roles' => ['organization/create']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['update'],
                            'roles' => ['organization/update']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['delete'],
                            'roles' => ['organization/delete']
                        ],
                    ]
                ]
            ]
        );
    }

    /**
     * Lists all Organization models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new OrganizationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Organization model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $contractSearchModel = new ContractSearch();
        $contractDataProvider = $contractSearchModel->search(Yii::$app->request->queryParams, organization_id: $id);

        return $this->render('view', [
            'contractSearchModel' => $contractSearchModel,
            'contractDataProvider' => $contractDataProvider,
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Organization model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Organization();

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
     * Updates an existing Organization model.
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
     * Deletes an existing Organization model.
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
            Yii::$app->session->setFlash('success', 'Организация успешно удалена.');
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
     * Finds the Organization model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Organization the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Organization::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Метод для использования в виджете Select2, отбирающий ограниченное количество результатов
     * с поиском по наименованию
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionList($term = null, $page = 1, $limit = 20, $empty = false)
    {
        if (Yii::$app->request->isAjax) {
            $out = ['more' => false, 'results' => []];
            $query = Organization::find();
            if ($empty) {
                $query->joinWith('user')->where(['user.organization_id' => null]);
            }
            $data = $query
                ->select([
                    'id' => '[[organization.id]]',
                    'text' => '[[organization.name]]',
                ])
                ->andFilterWhere(['like', 'organization.name', $term])
                ->orderBy(['organization.name' => SORT_ASC])
                ->groupBy('organization.id')
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
