<?php

/** @var yii\web\View $this */
/** @var common\models\User $user */

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
Здравствуйте, <?= $user->username ?>,

Перейдите по ссылке ниже, чтобы подтвердить свою почту:

<?= $verifyLink ?>
