<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Service $model */

$date_service = Yii::$app->formatter->asDatetime($model->completed_at, 'short');
?>

<div class="alert-work alert-confirm-service d-flex" data-key="<?= $model->id; ?>">
    <div class="service-container service-complete-datetime">
        <?= $date_service ?>
    </div>
    <div class="service-container service-content-text">
        <?= "В исследовании \"{$model->getShortTitle()}\" не полное количество проб, требуется подтверждение."; ?>
    </div>
    <div class="service-container service-button-view">
        <?= Html::a(
            'Перейти',
            ['service/view', 'id' => $model->id],
            ['class' => 'btn btn-light btn-sm btn-bordered']
        ); ?>
    </div>
</div>