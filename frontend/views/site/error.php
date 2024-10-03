<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">

    <h1><?= $this->title ?></h1>

    <div class="alert alert-danger">
        <?= nl2br($message) ?>
    </div>

    <p>
        Вышеупомянутая ошибка произошла во время обработки вашего запроса веб-сервером.
    </p>
    <p>
        Пожалуйста, свяжитесь с нами, если вы считаете, что это ошибка сервера. Спасибо.
    </p>

</div>