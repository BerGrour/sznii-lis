<?php

namespace frontend\controllers;

use common\models\ArchivePriceListSearch;
use common\models\Departament;
use common\models\PriceList;
use common\models\PriceListSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PriceListController implements the CRUD actions for PriceList model.
 */
class PriceListController extends Controller
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
                    'only' => ['index', 'view', 'create', 'update'],
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['index', 'view'],
                            'roles' => ['price_list/see']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create'],
                            'roles' => ['price_list/create']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['update'],
                            'roles' => ['price_list/update']
                        ],
                    ]
                ]
            ]
        );
    }

    /**
     * Lists all PriceList models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $lab = null;
        if (Yii::$app->user->identity->staff->job->departament->role == 'laboratory') {
            $lab = Yii::$app->user->identity->staff->job->departament_id;
        }
        $tabTitles = Departament::getLaboratoriesList('short_name', $lab);
        $tabs = [];
        $searchModel = new PriceListSearch();
        foreach ($tabTitles as $id => $value) {
            $tabs[$id] = $searchModel->search(
                Yii::$app->request->queryParams,
                departament_id: $id
            );
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'tabTitles' => $tabTitles,
            'tabs' => $tabs
        ]);
    }

    /**
     * Displays a single PriceList model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $archiveSearchModel = new ArchivePriceListSearch();
        $archiveDataProvider = $archiveSearchModel->search(Yii::$app->request->queryParams, research_id: $id);

        return $this->render('view', [
            'archiveSearchModel' => $archiveSearchModel,
            'archiveDataProvider' => $archiveDataProvider,
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new PriceList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new PriceList();
        $model->status = 1;

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
     * Updates an existing PriceList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->user->identity->staff->job->departament->role == 'laboratory'
            and Yii::$app->user->identity->staff->job->departament_id != $model->departament_id) {
                throw  new ForbiddenHttpException(Yii::t('app', 'Доступ ограничен.'));
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the PriceList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return PriceList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PriceList::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Метод для получения цены определенного исследования
     * @param string $research Наименование вида исследования
     * @return float|null
     */
    public function actionGetPrice($research)
    {
        $model = PriceList::findOne(['research' => $research]);
        return $model->price;
    }
}
