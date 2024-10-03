<?php

namespace frontend\tests\functional;

use common\fixtures\UserFixture;
use frontend\tests\FunctionalTester;

class VerifyEmailCest
{
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

    public function checkEmptyToken(FunctionalTester $I)
    {
        $I->amOnRoute('site/verify-email', ['token' => '']);
        $I->canSee('Bad Request', 'h1');
        $I->canSee('Токен электронной почты, не может быть пустым.');
    }

    public function checkInvalidToken(FunctionalTester $I)
    {
        $I->amOnRoute('site/verify-email', ['token' => 'wrong_token']);
        $I->canSee('Bad Request', 'h1');
        $I->canSee('Неправильный токен проверки электронной почты.');
    }

    public function checkNoToken(FunctionalTester $I)
    {
        $I->amOnRoute('site/verify-email');
        $I->canSee('Bad Request', 'h1');
        $I->canSee('Отсутствуют обязательные параметры: token');
    }

    public function checkAlreadyActivatedToken(FunctionalTester $I)
    {
        $I->amOnRoute('site/verify-email', ['token' => 'already_used_token_1548675330']);
        $I->canSee('Bad Request', 'h1');
        $I->canSee('Неправильный токен проверки электронной почты.');
    }

    public function checkSuccessVerification(FunctionalTester $I)
    {
        $I->amOnRoute('site/verify-email', ['token' => '4ch0qbfhvWwkcuWqjN8SWRq72SOw1KYT_1548675330']);
        $I->canSee('Ваша электронная почта была подтверждена!');
        $I->see('Выйти (rat_useless_24)', 'form button[type=submit]');

        $I->seeRecord('common\models\User', [
           'username' => 'rat_useless_24',
           'email' => 'rat_useless_24@mail.com',
           'status' => \common\models\User::STATUS_ACTIVE
        ]);
    }
}
