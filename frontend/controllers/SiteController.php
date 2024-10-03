<?php

namespace frontend\controllers;

use common\models\Batch;
use common\models\Service;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\User;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'login', 'signup', 'admin-panel'],
                'rules' => [
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['user/create']
                    ],
                    [
                        'actions' => ['admin-panel'],
                        'allow' => true,
                        'roles' => ['admin']
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $user = Yii::$app->user;

        if ($user->can('admin')) {
            return $this->redirect(['admin-panel']);
        } elseif ($user->can('registration')) {
            return $this->redirect(['batch/index']);
        } elseif ($user->can('laboratory')) {
            return $this->redirect(['service/index']);
        } elseif ($user->can('booker')) {
            return $this->redirect(['payment/index']);
        } elseif ($user->can('client')) {
            return $this->redirect(['client/index', 'org_id' => $user->identity->organization_id]);
        } else {
            return $this->redirect(['site/login']);
        }
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Благодарим Вас за обращение к нам. Мы ответим вам как можно скорее.');
            } else {
                Yii::$app->session->setFlash('error', 'При отправке вашего сообщения произошла ошибка.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            $auth = Yii::$app->authManager;
            $user = User::findByUsername($model->username, false);
            $role_name = $user->staff_id ? $user->staff->job->departament->role : 'client';
            $role = $auth->getRole($role_name);
            $auth->assign($role, $user->id);

            Yii::$app->session->setFlash('success', 'Пользователь зарегестрирован. Требуется подтверждение учетной записи, для этого перейдите по ссылке направленной пользователю по почте.');
            return $this->goHome();
        }

        if ($model->type == 'staff') {
            if (!$model->staff_id) {
                Yii::$app->session->setFlash('error', 'Нужно выбрать конкретного сотрудника.');
            }
            $model->organization_id = null;
        } elseif ($model->type == 'organization') {
            if (!$model->organization_id) {
                Yii::$app->session->setFlash('error', 'Нужно выбрать конкретную организацию.');
            }
            $model->staff_id = null;
        }
        $model->type = null;

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Проверьте свою электронную почту для получения дальнейших инструкций.');

                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'К сожалению, мы не можем сбросить пароль для указанного адреса электронной почты.');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Новый пароль сохранен.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if (($user = $model->verifyEmail()) && Yii::$app->user->login($user)) {
            Yii::$app->session->setFlash('success', 'Ваша электронная почта была подтверждена!');
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', 'К сожалению, мы не можем подтвердить вашу учетную запись с помощью предоставленного токена.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Проверьте свою электронную почту для получения дальнейших инструкций.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'К сожалению, мы не можем повторно отправить письмо с подтверждением на указанный адрес электронной почты.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    /**
     * Панель администратора
     */
    public function actionAdminPanel()
    {
        $confirmServices = Service::find()
            ->where(['not', ['completed_at' => NULL]])
            ->andWhere(['locked' => 0]);
        $alertConfirmProvider = new ActiveDataProvider([
            'query' => $confirmServices,
            'pagination' => ['pageSize' => 5]
        ]);

        $lateServices = Service::find()
            ->where(['completed_at' => null])
            ->andWhere(['<', 'predict_date', date('Y-m-d')]);
        $alertLateProvider = new ActiveDataProvider([
            'query' => $lateServices,
            'pagination' => ['pageSize' => 5]
        ]);

        $batch_idle_samples = Batch::find()
            ->joinWith('samples', false, 'INNER JOIN')
            ->where(['sample.busy' => 0])
            ->andWhere(['sample.losted_at' => null])
            ->groupBy('batch_id')->orderBy('batch.id DESC');

        $samplesProvider = new ActiveDataProvider([
            'query' => $batch_idle_samples,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSizeAdminPanel'],
                'forcePageParam' => false,
                'pageParam' => 'samples_page'
            ],
        ]);

        $subqueries = Batch::find()
            ->select('batch.id')
            ->joinWith('services')
            ->joinWith('samples')
            ->andWhere(['or',
                ['service.locked' => 0],
                ['sample.busy' => 0]
            ])
            ->groupBy('batch.id');

        $unPaymentBatches = Batch::find()
            ->where(['IS', 'payment_id', NULL]);

        if ($subqueries->count() > 0) {
            $unPaymentBatches->andWhere(['NOT IN', 'id', $subqueries]);
        }

        $unPaymentBatchesProvider = new ActiveDataProvider([
            'query' => $unPaymentBatches,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSizeAdminPanel'],
                'forcePageParam' => false,
                'pageParam' => 'services_page'
            ],
        ]);

        return $this->render('admin_panel', [
            'alertConfirmProvider' => $alertConfirmProvider,
            'alertLateProvider' => $alertLateProvider,
            'unPaymentBatchesProvider' => $unPaymentBatchesProvider,
            'samplesProvider' => $samplesProvider
        ]);
    }
}
