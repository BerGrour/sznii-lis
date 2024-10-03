<?php

use common\models\Departament;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use kartik\time\TimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Sample $model */
/** @var yii\widgets\ActiveForm $form */

$this->title = 'Изменение пробы: ' . $model->getShortTitle();
$this->params['breadcrumbs'][] = ['label' => 'Партии проб', 'url' => ['batch/index']];
$this->params['breadcrumbs'][] = ['label' => $model->batch->getShortTitle(), 'url' => ['batch/view', 'id' => $model->batch_id]];
$this->params['breadcrumbs'][] = ['label' => $model->getShortTitle(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="sample-update">

    <h1><?= $this->title ?></h1>

    <div class="sample-form form-with-margins">

        <?php $form = ActiveForm::begin([
            'id' => 'form-sample-update',
            'action' => Url::to(['/sample/update', 'id' => $model->id]),
            'options' => ['class' => 'short-form', 'data-pjax' => true],
        ]); ?>

            <?= $form->field($model, 'identificator')->textInput() ?>

            <?= $form->field($model, 'departament_id')->widget(Select2::class, [
                'data' => ArrayHelper::map(Departament::find()->where(['role' => 'laboratory'])->orderBy(['title' => SORT_ASC])->all(), 'id', 'title'),
                'language' => 'ru',
                'options' => ['placeholder' => 'Выберите лабораторию...'],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ]) ?>

            <div class="form-group form-group-horizontaly sample-lost_at">
                <label class="control-label">Дата и время потери</label>

                <?= $form->field($model, 'lost_date')->widget(DatePicker::class, [
                    'name' => 'datepicker-sample-lost_date',
                    'language' => 'ru',
                    'type' => DatePicker::TYPE_INPUT,
                    'pluginOptions' => [
                        'todayHighlight' => true,
                        'autoclose' => true,
                        'orientation' => 'bottom',
                        'format' => 'yyyy-mm-dd',
                    ]
                ]) ?>

                <?= $form->field($model, 'lost_time')->widget(TimePicker::class, [
                    'pluginOptions' => [
                        'showMeridian' => false,
                        'minuteStep' => 5,
                        'defaultTime' => false
                    ]
                ]) ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Назад', Yii::$app->request->referrer, ['class' => 'btn btn-secondary']) ?>
            </div>

        <?php ActiveForm::end(); ?>

        <div class="card-wrapper">
            <div class="custom-card" id="sample-form-info">
                <h5 class="card-header">Важно!</h5>
                <div class="card-body">
                    <p class="card-text">При изменении информации о пробе требуется соблюдать соответствующий формат идентификатора. Иначе это может вызвать путаницу!</p>
                    <p class="card-text">Так при изменении лаборатории, необходимо изменить аббревиатуру идентификатора на соответствующую аббревиатуру новой лаборатории.</p>
                </div>
            </div>
        </div>

    </div>
</div>