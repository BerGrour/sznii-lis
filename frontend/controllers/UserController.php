<?php

namespace frontend\controllers;

use common\models\User;
use common\models\UserSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
                            'roles' => ['user/see']
                        ],
                    ]
                ]
            ]
        );
    }

    /**
     * Lists all Staff models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();

        $staffsDataProvider = $searchModel->search(Yii::$app->request->queryParams, 1);
        $orgsDataProvider = $searchModel->search(Yii::$app->request->queryParams, 2);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'staffsDataProvider' => $staffsDataProvider,
            'orgsDataProvider' => $orgsDataProvider,
        ]);
    }

    /**
     * Displays a single Staff model.
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
     * Finds the Staff model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
