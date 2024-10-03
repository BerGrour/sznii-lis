<?php

use common\models\Departament;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\PriceList $model */
/** @var yii\widgets\ActiveForm $form */

$this->title = 'Создание нового исследования';
$this->params['breadcrumbs'][] = ['label' => 'Каталог исследований', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="price-list-create">

    <h1><?= $this->title ?></h1>

    <div class="price-list-form form-with-margins">

        <?php $form = ActiveForm::begin([
            'options' => ['class' => 'short-form'],
        ]); ?>

            <?= $form->field($model, 'departament_id')->widget(Select2::class, [
                'data' => ArrayHelper::map(Departament::find()->where(['role' => 'laboratory'])->orderBy(['title' => SORT_ASC])->all(), 'id', 'title'),
                'language' => 'ru',
                'options' => ['placeholder' => 'Выберите лабораторию...'],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ]) ?>

            <?= $form->field($model, 'research')->textInput([
                'maxlength' => true,
            ]) ?>

            <?= $form->field($model, 'price')->textInput([
                'type' => 'number',
                'min' => 0,
                'step' => '0.01',
            ]) ?>

            <?= $form->field($model, 'status')->radioList([
                1 => $model::STATUS_ACTIVE,
                0 => $model::STATUS_INACTIVE
            ], ['class' => 'compact-radio-group']); ?>

            <?= $form->field($model, 'period')->textInput([
                'type' => 'number',
                'min' => 1,
                'step' => '1',
            ]) ?>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Назад', Yii::$app->request->referrer, ['class' => 'btn btn-secondary']) ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>