<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Batch $model */

$date_batch = Yii::$app->formatter->asDatetime($model->employed_at, 'short');
$amount = $model->getServices()->count();
?>

<div class="alert-work alert-completed-batch d-flex" data-key="<?= $model->id; ?>">
    <div class="batch-container batch-employ-datetime">
        <?= "Партия проб от {$date_batch} завершена" ?>
    </div>
    <div class="batch-container batch-content-count">
        <?php if ($amount == 1) { ?>
            <?= "Проведено {$amount} исследование"; ?>
        <?php } elseif ($amount < 5) { ?>
            <?= "Проведено {$amount} исследования"; ?>
        <?php } else { ?>
            <?= "Проведено {$amount} исследований"; ?>
        <?php } ?>
    </div>
    <div class="batch-container batch-button-create">
        <?= Html::a(
            'Регистрация акта',
            ['create', 'batch_id' => $model->id],
            ['class' => 'btn btn-light btn-sm btn-bordered']
        ); ?>
    </div>
</div>