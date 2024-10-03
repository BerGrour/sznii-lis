<?php

use common\models\Departament;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Staff $model */

$this->title = 'Создание сотрудника';
$this->params['breadcrumbs'][] = ['label' => 'Сотрудники', 'url' => ['staff/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="staff-create">

    <h1><?= $this->title ?></h1>

    <div class="service-form form-with-margins">

        <?php $form = ActiveForm::begin([
            'options' => ['class' => 'short-form'],
        ]); ?>

            <?= $form->field($model, 'departament_select')->dropDownList(
                ArrayHelper::map(Departament::find()->orderBy(['title' => SORT_ASC])->all(), 'id', 'title'),
                [
                    'id' => 'departament-field-drop',
                    'prompt' => 'Выберите отдел...'
                ],
            ); ?>

            <?= $form->field($model, 'job_select')->widget(DepDrop::class, [
                'options' => [
                    'id' => 'job-field-dep_drop',
                ],
                'pluginOptions' => [
                    'depends' => ['departament-field-drop'],
                    'url' => Url::to(['/job/departament-jobs']),
                    'placeholder' => 'Выберите должность...'
                ]
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
</div>