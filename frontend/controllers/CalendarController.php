<?php

namespace frontend\controllers;

use common\models\CalendarDate;
use common\models\CalendarYear;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * CalendarController реализация производственного календаря
 */
class CalendarController extends Controller
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
                            'roles' => ['calendar/see']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create'],
                            'roles' => ['calendar/create']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['update', 'set-event', 'unset-event'],
                            'roles' => ['calendar/update']
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * Отображает календарь за указанный год
     * 
     * @param int $year_number
     * @return string
     */
    public function actionView($year)
    {
        $model = self::findYear($year);
        $events = ArrayHelper::map($model->getDates()->all(), 'id', 'date');

        return $this->render('view', [
            'year' => $model,
            'events' => $events
        ]);
    }

    /**
     * Creates a new CalendarYear model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new CalendarYear();
        $last_year = CalendarYear::find()->orderBy(['number' => SORT_DESC])->one();
        if ($last_year) {
            $model->number = $last_year->number + 1;
        } else {
            $model->number = date('Y');
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'year' => $model->number]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Поиск по году
     * @param int $year Год
     * @return CalendarYear Модель
     * @throws NotFoundHttpException Если ничего не найдено
     */
    protected function findYear($year)
    {
        if (($model = CalendarYear::findOne(['number' => $year])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Изменение календаря
     * @param int $year год
     * @return string
     */
    public function actionUpdate($year)
    {
        $model = $this->findYear($year);
        $events = ArrayHelper::map($model->getDates()->all(), 'id', 'date');

        return $this->render('update', [
            'year' => $model,
            'events' => $events
        ]);
    }

    /**
     * Действие на добавление выходного дня в систему
     * @param int $year_id индекс года
     * @param string $date дата в формате 'dd.mm'
     * @return bool[]
     */
    public function actionSetEvent($year_id, $date)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new CalendarDate();
        $model->year_id = $year_id;
        $model->date = $date;
        if ($model->save()) {
            return ['success' => true];
        }
        Yii::$app->session->setFlash('error', 'Произошла неизвестная ошибка!');
        return ['success' => false];
    }

    /**
     * Действие на исключение выходного дня из системы
     * @param int $year_id индекс года
     * @param string $date дата в формате 'dd.mm'
     * @return bool[]
     */
    public function actionUnsetEvent($year_id, $date)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = CalendarDate::find()
            ->where(['year_id' => $year_id, 'date' => $date])
            ->one();
        if ($model->delete()) {
            return ['success' => true];
        }
        Yii::$app->session->setFlash('error', 'Произошла неизвестная ошибка!');
        return ['success' => false];
    }
}
