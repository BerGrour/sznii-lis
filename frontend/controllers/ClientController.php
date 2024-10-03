<?php

namespace frontend\controllers;

use common\models\Organization;
use common\models\Service;
use common\models\ServiceSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;

/**
 * ClientController контроллер для клиентов
 */
class ClientController extends Controller
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
                    'only' => ['index', /*'view'*/],
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['index', /*'view'*/],
                            'roles' => ['client']
                        ],
                    ]
                ]
            ]
        );
    }

    /**
     * Отображает страницу клиента
     * @param int $id ID
     * @return string
     * @throws ForbiddenHttpException если пользователь не принадлежит этой организации
     */
    public function actionIndex($org_id)
    {
        if ($org_id == Yii::$app->user->identity->organization_id) {
            $organization = Organization::findOne(['id' => $org_id]);

            $servicesSearchModel = new ServiceSearch();
            $servicesDataProvider = $servicesSearchModel->search(Yii::$app->request->queryParams, organization_id: $org_id);

            return $this->render('index', [
                'organization' => $organization,
                'servicesSearchModel' => $servicesSearchModel,
                'servicesDataProvider' => $servicesDataProvider
            ]);
        }
        throw new ForbiddenHttpException(Yii::t('app', 'Доступ ограничен.'));
    }

    // public function actionView($service_id)
    // {
    //     $service = Service::findOne(['id' => $service_id]);
    //     $org_id = $service->batch->contract->organization_id;

    //     if ($org_id == Yii::$app->user->identity->organization_id) {
    //         return $this->render('view', [
    //             'model' => $service
    //         ]);
    //     }
    //     throw new ForbiddenHttpException(Yii::t('app', 'Доступ ограничен.'));
    // }
}
