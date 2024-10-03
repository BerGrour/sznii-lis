<?php

/** @var yii\web\View $this */
/** @var common\models\Payment $model */

$this->title = 'Изменение акта оплаты: ' . $model->getShortTitle();
$this->params['breadcrumbs'][] = ['label' => 'Акты оплаты', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getShortTitle(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="payment-update">

    <h1><?= $this->title ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>