<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var common\models\CalendarYear $model */

$this->title = 'Создание календаря';
$this->params['breadcrumbs'][] = 'Производственный календарь';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="calendar_year-create">

    <h1><?= $this->title ?></h1>

    <div class="calendar_year-form form-with-margins">
        <?php $form = ActiveForm::begin([
            'id' => 'form-calendar_year',
            'options' => ['class' => 'short-form', 'data-pjax' => false],
        ]); ?>

            <?= $form->field($model, 'number')->textInput([
                'type' => 'number',
                'maxlength' => true
            ]) ?>

            <div class="form-group">
                <?= Html::submitButton('Создать', [
                    'class' => 'btn btn-success',
                    'data' => [
                        'confirm' => 'Вы уверены что хотите создать календарь вручную? Метки на выходные дни придется выставлять вручную!'
                    ]
                ]); ?>
                <?= Html::a('Назад', Yii::$app->request->referrer, ['class' => 'btn btn-secondary']) ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>