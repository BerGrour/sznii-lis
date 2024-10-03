<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Organization $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="organization-form form-with-margins">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'short-form'],
    ]); ?>

        <?= $form->field($model, 'name')->textInput([
            'maxlength' => true,
        ]) ?>

        <?= $form->field($model, 'inn')->textInput([
            'maxlength' => true,
        ]) ?>

        <?= $form->field($model, 'address')->textInput([
            'maxlength' => true,
        ]) ?>

        <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::class, [
            'mask' => '+7 (999) 999-99-99',
        ]); ?>

        <?= $form->field($model, 'email')->textInput([
            'maxlength' => true,
        ]) ?>

        <?= $form->field($model, 'director')->textInput([
            'maxlength' => true,
        ]) ?>

        <?= $form->field($model, 'comment')->textarea([
            'rows' => 3,
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Назад', Yii::$app->request->referrer, ['class' => 'btn btn-secondary']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>