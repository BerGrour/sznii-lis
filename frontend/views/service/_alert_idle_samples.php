<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Batch $model */

$amount = $model->getIdleSamplesCount();

if ($amount == 1) {
    $title_link = "Партия из <strong>{$amount}</strong> незанятой пробы";
} else {
    $title_link = "Партия из <strong>{$amount}</strong> незанятых проб";
}
$batch_content = Html::a($title_link, ['/batch/view', 'id' => $model->id])
?>

<div class="alert-work alert-idle-samples d-flex" data-key="<?= $model->id; ?>">
    <div class="batch-container batch-employ-datetime">
        <?= Yii::$app->formatter->asDatetime(
            $model->employed_at,
            'php:j mm, H:i:s'
        ); ?>
    </div>
    <div class="batch-container batch-content-count">
        <?= $batch_content ?>
    </div>
    <div class="batch-container batch-button-create">
        <?= Html::a(
            'Начать исследование',
            ['create-idle', 'batch_id' => $model->id],
            ['class' => 'btn btn-light btn-sm btn-bordered']
        ); ?>
    </div>
</div>