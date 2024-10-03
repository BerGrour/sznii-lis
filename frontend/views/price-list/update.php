<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var yii\widgets\ActiveForm $form */
/** @var common\models\PriceList $model */

$this->title = 'Изменение цены на исследование: ' . $model->getShortTitle();
$this->params['breadcrumbs'][] = ['label' => 'Каталог исследований', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getShortTitle(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="price-list-update">

    <h1><?= $this->title ?></h1>

    <div class="price-list-form form-with-margins">

        <?php $form = ActiveForm::begin([
            'options' => ['class' => 'short-form'],
        ]); ?>

            <?= $form->field($model, 'price')->textInput([
                'type' => 'number',
                'min' => 0,
                'step' => '0.01',
            ]) ?>

            <?= $form->field($model, 'period')->textInput([
                'type' => 'number',
                'min' => 1,
                'step' => '1',
            ]) ?>

            <?= $form->field($model, 'status')->radioList([
                1 => $model::STATUS_ACTIVE,
                0 => $model::STATUS_INACTIVE
            ], ['class' => 'compact-radio-group']); ?>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Назад', Yii::$app->request->referrer, ['class' => 'btn btn-secondary']) ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>