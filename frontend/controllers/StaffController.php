<?php

namespace frontend\controllers;

use common\models\Job;
use common\models\Staff;
use common\models\StaffSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * StaffController implements the CRUD actions for Staff model.
 */
class StaffController extends Controller
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
                    'only' => ['index', 'view', 'create', 'update', 'delete', 'restore', 'create-adaptive', 'transfer-departament'],
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['index', 'view'],
                            'roles' => ['staff/see']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create'],
                            'roles' => ['staff/create']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['update', 'transfer'],
                            'roles' => ['staff/update']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['delete', 'restore'],
                            'roles' => ['staff/delete']
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create-adaptive'],
                            'roles' => ['manageRoles']
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * Метод для блокировки доступа к редактированию главной админки
     * @param int $id
     * @throws \yii\web\ForbiddenHttpException
     * @return void
     */
    static function blockOnAdmin($id)
    {
        if ($id == 1 and Yii::$app->user->id != 1) {
            throw new ForbiddenHttpException(Yii::t('app', 'Доступ ограничен.'));
        }
    }

    /**
     * Lists all Staff models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new StaffSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, active: false);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
        self::blockOnAdmin($id);

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Staff model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * 
     * @param int $job_id индекс должности
     * @return string|\yii\web\Response
     */
    public function actionCreate($job_id)
    {
        $model = new Staff(['scenario' => Staff::SCENARIO_CREATE_FROM_JOB]);
        $model->job_id = $job_id;

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
     * Создание нового сотрудника через панель администратора
     * с более гибкими полями выбора отдела и должности
     * 
     * @return string|\yii\web\Response
     */
    public function actionCreateAdaptive()
    {
        $model = new Staff(['scenario' => Staff::SCENARIO_CREATE_ADVANCED]);

        if ($this->request->isPost) {
            $model->job_id = Yii::$app->request->post('Staff')['job_select'];;
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }
        return $this->render('create_advanced', [
            'model' => $model
        ]);
    }

    /**
     * Updates an existing Staff model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        self::blockOnAdmin($id);

        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Staff model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        self::blockOnAdmin($id);

        $model = $this->findModel($id);

        if (!$model->leave_date) {
            try {
                $model->delete();
                Yii::$app->session->setFlash('success', 'Сотрудник успешно удален.');
                return $this->redirect(['/job/view', 'id' => $model->job_id]);
            } catch (yii\db\Exception $e) {
                if ($e->errorInfo[1] == 1451) {
                    $model->leave_date = date('Y-m-d H:i:s');
                    if ($model->save(false)) {
                        $user = $model->user;
                        $user->status = 0;
                        $user->save(false);
                    }
                    Yii::$app->session->setFlash('success', 'Сотруднику назначен статус "Уволен".');
                } else {
                    Yii::$app->session->setFlash('error', 'Неизвестная ошибка при удалении.');
                }
            }
        } else {
            Yii::$app->session->setFlash('error', 'Сотрудник уже уволен!');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Действие на восстановление сотрудника и его аккаунта
     * @param int $id индекс сотрудника
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionRestore($id)
    {
        self::blockOnAdmin($id);

        $model = $this->findModel($id);

        $model->leave_date = null;
        $model->employ_date = date('Y-m-d H:i:s');
        if ($model->save(false) and $model->user) {
            $user = $model->user;
            $user->status = 10;
            $user->save(false);
            Yii::$app->session->setFlash('success', 'Сотрудник успешно восстановлен.');
        } else {
            throw new NotFoundHttpException('Восстановление невозможно!');
        }
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Finds the Staff model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Staff the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Staff::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Метод для использования в виджете Select2, отбирающий ограниченное количество результатов
     * с поиском по фамилии сотрудника
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionList($term = null, $page = 1, $limit = 20, $empty = false)
    {
        if (Yii::$app->request->isAjax) {
            $out = ['more' => false, 'results' => []];
            $query = Staff::find();
            if ($empty) {
                $query->joinWith('user')->where(['user.staff_id' => null]);
            }
            $data = $query
                ->select([
                    'id' => '[[staff.id]]',
                    'text' => '[[staff.fio]]',
                ])
                ->andFilterWhere(['like', 'staff.fio', $term])
                ->orderBy(['staff.fio' => SORT_ASC])
                ->groupBy('staff.id')
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

    /**
     * Действие для перевода сотрудника в другой отдел вместе со сменой роли
     * @param int $id индекс пользователя
     * @return string|\yii\web\Response
     */
    public function actionTransfer($id)
    {
        self::blockOnAdmin($id);

        $staff = $this->findModel($id);
        $staff->scenario = Staff::SCENARIO_TRANSFER;

        if (!isset($staff->user)) {
            Yii::$app->session->setFlash('error', 'У сотрудника нет учетной записи!');
            return $this->redirect(['view', 'id' => $staff->id]);
        }
        if ($this->request->isPost) {
            $new_job = Job::findOne(Yii::$app->request->post('Staff')['job_select']);
            if ($new_job and $staff->job_id != $new_job->id) {
                $cur_role = $staff->job->departament->role;
                $new_role = $new_job->departament->role;
                if ($cur_role != $new_role) {
                    $auth = Yii::$app->authManager;
                    $item = $auth->getRole($cur_role);
                    $auth->revoke($item, $staff->user->id);
                    $role = $auth->getRole($new_role);
                    $auth->assign($role, $staff->user->id);
                }
                $staff->job_id = $new_job->id;
                if ($staff->save(false)) {
                    Yii::$app->session->setFlash('success', 'Сотрудник успешно переведен.');
                    return $this->redirect(['view', 'id' => $id]);
                }
            } else {
                Yii::$app->session->setFlash('error', 'Выберите другую должность!');
            }
        }

        return $this->render('transfer', [
            'model' => $staff,
        ]);
    }
}
