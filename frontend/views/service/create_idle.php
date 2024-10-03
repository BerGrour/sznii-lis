<?php

use common\models\PriceList;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Service $model */
/** @var common\models\Batch $batch */
/** @var yii\widgets\ActiveForm $form */

$this->title = 'Создание нового исследования';
$this->params['breadcrumbs'][] = ['label' => 'Исследования', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-create-idle">

    <h1><?= $this->title ?></h1>

    <div class="service-form form-with-margins">

        <?php $form = ActiveForm::begin([
            'options' => ['class' => 'short-form'],
        ]); ?>

            <?= $form->field($model, 'research_id')->widget(Select2::class, [
                'data' => ArrayHelper::map(PriceList::getListLaboratoryResearches(Yii::$app->user->identity->staff->job->departament_id), 'id', 'research'),
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Выберите вид исследования...',
                    'id' => 'research-field-select'
                ],
            ]); ?>

            <div class="form-group form-group-horizontaly service-idle-amount">
                <?= $form->field($model, 'amount')->textInput([
                    'type' => 'number',
                    'min' => 1,
                    'max' => $batch->getIdleSamplesCount()
                ]); ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Назад', Yii::$app->request->referrer, ['class' => 'btn btn-secondary']) ?>
            </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>