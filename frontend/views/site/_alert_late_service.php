<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Service $model */

$predict_date = Yii::$app->formatter->asDate($model->predict_date, 'short');
?>

<div class="alert-work alert-late-service d-flex" data-key="<?= $model->id; ?>">
    <div class="service-container service-predict-date">
        <?= $predict_date ?>
    </div>
    <div class="service-container service-content-text">
        <?= "Исследование \"{$model->getShortTitle()}\" опаздывает по сроку завершения!"; ?>
    </div>
    <div class="service-container service-button-view">
        <?= Html::a(
            'Перейти',
            ['service/view', 'id' => $model->id],
            ['class' => 'btn btn-light btn-sm btn-bordered']
        ); ?>
    </div>
</div>