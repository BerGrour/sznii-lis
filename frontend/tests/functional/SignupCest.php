<?php

namespace frontend\tests\functional;

use common\fixtures\StaffFixture;
use common\fixtures\UserFixture;
use frontend\tests\FunctionalTester;

class SignupCest
{
    protected $formId = '#form-signup';

    /**
     * Load fixtures before db transaction begin
     * Called in _before()
     * @see \Codeception\Module\Yii2::_before()
     * @see \Codeception\Module\Yii2::loadFixtures()
     * @return array
     */
    public function _fixtures()
    {
        return [
            'staff' => [
                'class' => StaffFixture::class,
                'dataFile' => codecept_data_dir() . 'staff.php',
            ],
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php',
            ],
        ];
    }

    public function _before(FunctionalTester $I)
    {
        $I->amLoggedInAs(1);
        $I->amOnRoute('site/signup');
    }

    public function signupWithEmptyFields(FunctionalTester $I)
    {
        $I->see('Регистрация', 'h1');
        $I->see('Пожалуйста, заполните следующие поля для регистрации пользователя:');
        $I->submitForm($this->formId, []);
        $I->seeValidationError('Необходимо заполнить «Логин».');
        $I->seeValidationError('Необходимо заполнить «Email».');
        $I->seeValidationError('Необходимо заполнить «Пароль».');
        $I->seeValidationError('Необходимо заполнить «Для кого учетная запись?».');

    }

    public function signupWithWrongEmail(FunctionalTester $I)
    {
        $I->submitForm(
            $this->formId, [
            'SignupForm[username]'  => 'tsttst',
            'SignupForm[email]'     => 'ttttt',
            'SignupForm[password]'  => 'tsttst',
            'SignupForm[type]'  => 'staff',
            'SignupForm[staff_id]'  => '5',
        ]
        );
        $I->dontSee('Необходимо заполнить «Логин».', '.invalid-feedback');
        $I->dontSee('Необходимо заполнить «Пароль».', '.invalid-feedback');
        $I->see('Значение «Email» не является правильным email адресом.', '.invalid-feedback');
    }

    public function signupSuccessfully(FunctionalTester $I)
    {
        $I->submitForm($this->formId, [
            'SignupForm[username]' => 'tester',
            'SignupForm[email]' => 'tester.email@example.com',
            'SignupForm[password]' => 'tester_password',
            'SignupForm[type]'  => 'staff',
            'SignupForm[staff_id]'  => '5',
        ]);

        $I->seeRecord('common\models\User', [
            'username' => 'tester',
            'email' => 'tester.email@example.com',
            'status' => \common\models\User::STATUS_INACTIVE
        ]);

        $I->seeEmailIsSent();
        $I->see('Пользователь зарегестрирован. Требуется подтверждение учетной записи, для этого перейдите по ссылке направленной пользователю по почте.');
    }
}
