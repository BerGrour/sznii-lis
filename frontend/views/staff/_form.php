<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Staff $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="staff-form form-with-margins">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'short-form'],
    ]); ?>

        <?= $form->field($model, 'fio')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'employ_date')->widget(DatePicker::class, [
            'name' => 'datepicker-contract-employ_date',
            'language' => 'ru',
            'type' => DatePicker::TYPE_INPUT,
            'pluginOptions' => [
                'todayHighlight' => true,
                'autoclose' => true,
                'orientation' => 'bottom',
                'format' => 'yyyy-mm-dd',
            ]
        ]) ?>

        <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::class, [
            'mask' => '+7 (999) 999-99-99',
        ]); ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Назад', Yii::$app->request->referrer, ['class' => 'btn btn-secondary']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>