<?php

use common\models\Departament;
use common\models\User;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Departament $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="departament-form form-with-margins">

    <?php $form = ActiveForm::begin([
        'id' => 'form-departament',
        'options' => ['class' => 'short-form', 'data-pjax' => true],
    ]); ?>

        <?= $form->field($model, 'title')->textInput([
            'maxlength' => true,
        ]) ?>

        <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::class, [
            'mask' => '+7 (999) 999-99-99',
        ]); ?>

        <?= $form->field($model, 'role')->radioList(
            array_column(User::getListRoles(), 'description', 'name'),
            ['class' => 'compact-radio-group', 'encode' => false]
        ); ?>

        <?php $short_name_field = $form->field($model, 'short_name')->textInput(['maxlength' => true]); ?>

        <?php if (!empty($model->id) and $model->role == 'laboratory') { ?>
            <div class="form-group" id="short_name-field">
                <?= $short_name_field ?>
            </div>
        <?php } else { ?>
            <div class="form-group" id="short_name-field" style="display: none;">
                <?= $short_name_field ?>
            </div>
        <?php } ?>

        <?php $abbreviation_field = $form->field($model, 'abbreviation')->textInput(['maxlength' => true])
            ->hint("Буква, которая будет указана в идентификаторах проб"); ?>

        <?php if ($model->id and $model->role == 'laboratory') { ?>
            <div class="form-group" id="abbreviation-field">
                <?= $abbreviation_field ?>
            </div>
        <?php } else { ?>
            <div class="form-group" id="abbreviation-field" style="display: none;">
                <?= $abbreviation_field ?>
            </div>
        <?php } ?>

        <?php $period_field = $form->field($model, 'period')->widget(Select2::class, [
            'data' => [Departament::PERIOD_YEAR => 'Год', Departament::PERIOD_MONTH => 'Месяц'],
            'language' => 'ru',
            'options' => ['placeholder' => ''],
            'pluginOptions' => ['allowClear' => true]
        ])->hint("Периодичность обновления порядкового номера в идентификаторе пробы: \"К24.05.25.<strong>1</strong>\""); ?>

        <?php if ($model->id and $model->role == 'laboratory') { ?>
            <div class="form-group" id="period-field">
                <?= $period_field ?>
            </div>
        <?php } else { ?>
            <div class="form-group" id="period-field" style="display: none;">
                <?= $period_field ?>
            </div>
        <?php } ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Назад', Yii::$app->request->referrer, ['class' => 'btn btn-secondary']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>