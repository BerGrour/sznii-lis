<?php

namespace frontend\tests\unit\models;


use Codeception\Test\Unit;
use common\fixtures\UserFixture;
use frontend\models\ResendVerificationEmailForm;

class ResendVerificationEmailFormTest extends Unit
{
    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;


    public function _before()
    {
        $this->tester->haveFixtures([
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php'
            ]
        ]);
    }

    public function testWrongEmailAddress()
    {
        $model = new ResendVerificationEmailForm();
        $model->attributes = [
            'email' => 'aaa@bbb.cc'
        ];

        verify($model->validate())->false();
        verify($model->hasErrors())->true();
        verify($model->getFirstError('email'))->equals('Нет пользователя с таким адресом email.');
    }

    public function testEmptyEmailAddress()
    {
        $model = new ResendVerificationEmailForm();
        $model->attributes = [
            'email' => ''
        ];

        verify($model->validate())->false();
        verify($model->hasErrors())->true();
        verify($model->getFirstError('email'))->equals('Необходимо заполнить «Email».');
    }

    public function testResendToActiveUser()
    {
        $model = new ResendVerificationEmailForm();
        $model->attributes = [
            'email' => 'test2@mail.com'
        ];

        verify($model->validate())->false();
        verify($model->hasErrors())->true();
        verify($model->getFirstError('email'))->equals('Нет пользователя с таким адресом email.');
    }

    public function testSuccessfullyResend()
    {
        $model = new ResendVerificationEmailForm();
        $model->attributes = [
            'email' => 'rat_useless_24@mail.com'
        ];

        verify($model->validate())->true();
        verify($model->hasErrors())->false();

        verify($model->sendEmail())->true();
        $this->tester->seeEmailIsSent();

        $mail = $this->tester->grabLastSentEmail();

        verify($mail)->instanceOf('yii\mail\MessageInterface');
        verify($mail->getTo())->arrayHasKey('rat_useless_24@mail.com');
        verify($mail->getFrom())->arrayHasKey(\Yii::$app->params['supportEmail']);
        verify($mail->getSubject())->equals('Аккаунт зарегистрирован на ' . \Yii::$app->name);
        verify($mail->toString())->stringContainsString('4ch0qbfhvWwkcuWqjN8SWRq72SOw1KYT_1548675330');
    }
}
