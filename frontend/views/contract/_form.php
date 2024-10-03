<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Contract $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="contract-form form-with-margins">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'short-form'],
    ]); ?>

        <?= $form->field($model, 'number')->textInput(['type' => 'number', 'min' => 0]) ?>

        <?= $form->field($model, 'start_date')->widget(DatePicker::class, [
            'name' => 'datepicker-contract-start_date',
            'language' => 'ru',
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            'pluginOptions' => [
                'todayHighlight' => true,
                'autoclose' => true,
                'orientation' => 'bottom',
                'format' => 'yyyy-mm-dd',
            ]
        ]) ?>

        <?= $form->field($model, 'end_date')->widget(DatePicker::class, [
            'name' => 'datepicker-contract-end_date',
            'language' => 'ru',
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            'pluginOptions' => [
                'todayHighlight' => true,
                'autoclose' => true,
                'orientation' => 'bottom',
                'format' => 'yyyy-mm-dd',
            ]
        ]) ?>

        <?= $form->field($model, 'list_date')->widget(DatePicker::class, [
            'name' => 'datepicker-contract-list_date',
            'language' => 'ru',
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            'pluginOptions' => [
                'todayHighlight' => true,
                'autoclose' => true,
                'orientation' => 'bottom',
                'format' => 'yyyy-mm-dd',
            ]
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Назад', Yii::$app->request->referrer, ['class' => 'btn btn-secondary']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>