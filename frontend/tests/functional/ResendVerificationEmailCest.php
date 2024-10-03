<?php

namespace frontend\tests\functional;

use common\fixtures\UserFixture;
use frontend\tests\FunctionalTester;

class ResendVerificationEmailCest
{
    protected $formId = '#resend-verification-email-form';


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
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php',
            ],
        ];
    }

    public function _before(FunctionalTester $I)
    {
        $I->amOnRoute('/site/resend-verification-email');
    }

    protected function formParams($email)
    {
        return [
            'ResendVerificationEmailForm[email]' => $email
        ];
    }

    public function checkPage(FunctionalTester $I)
    {
        $I->see('Выслать письмо для подтверждения', 'h1');
        $I->see('Пожалуйста, укажите свой адрес электронной почты. Туда будет отправлено письмо с подтверждением.');
    }

    public function checkEmptyField(FunctionalTester $I)
    {
        $I->submitForm($this->formId, $this->formParams(''));
        $I->seeValidationError('Необходимо заполнить «Email».');
    }

    public function checkWrongEmailFormat(FunctionalTester $I)
    {
        $I->submitForm($this->formId, $this->formParams('abcd.com'));
        $I->seeValidationError('Значение «Email» не является правильным email адресом.');
    }

    public function checkWrongEmail(FunctionalTester $I)
    {
        $I->submitForm($this->formId, $this->formParams('wrong@email.com'));
        $I->seeValidationError('Нет пользователя с таким адресом email.');
    }

    public function checkAlreadyVerifiedEmail(FunctionalTester $I)
    {
        $I->submitForm($this->formId, $this->formParams('administrator@administrator.com'));
        $I->seeValidationError('Нет пользователя с таким адресом email.');
    }

    public function checkSendSuccessfully(FunctionalTester $I)
    {
        $I->submitForm($this->formId, $this->formParams('rat_useless_24@mail.com'));
        $I->canSeeEmailIsSent();
        $I->seeRecord('common\models\User', [
            'email' => 'rat_useless_24@mail.com',
            'username' => 'rat_useless_24',
            'status' => \common\models\User::STATUS_INACTIVE
        ]);
        $I->see('Проверьте свою электронную почту для получения дальнейших инструкций.');
    }
}
