<?php

/** @var yii\web\View $this */
/** @var common\models\Payment $model */

$this->title = 'Создание акта оплаты';
$this->params['breadcrumbs'][] = ['label' => 'Акты оплаты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-create">

    <h1><?= $this->title ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>