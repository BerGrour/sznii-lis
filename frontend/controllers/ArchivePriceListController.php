<?php

namespace frontend\controllers;

use common\models\ArchivePriceList;
use common\models\ArchivePriceListSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ArchivePriceListController implements the CRUD actions for ArchivePriceList model.
 */
class ArchivePriceListController extends Controller
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
                            'actions' => ['index'],
                            'roles' => ['archive_price_list/see']
                        ],
                    ]
                ]
            ]
        );
    }

    /**
     * Lists all ArchivePriceList models.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->user->identity->staff->job->departament->role == 'laboratory') {
            $lab = Yii::$app->user->identity->staff->job->departament_id;
        }
        $searchModel = new ArchivePriceListSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            departament_id: $lab
        );

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the ArchivePriceList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ArchivePriceList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ArchivePriceList::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
