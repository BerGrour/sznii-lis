<?php

use common\models\Batch;
use common\models\PriceList;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Service $model */
/** @var yii\widgets\ActiveForm $form */

$this->title = 'Создание нового пустого исследования';
$this->params['breadcrumbs'][] = ['label' => 'Исследования', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-create-empty">

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

            <?= $form->field($model, 'batch_id')->widget(Select2::class, [
                'data' => ArrayHelper::map(Batch::find()->joinWith('samples', false, 'INNER JOIN')->where(['sample.departament_id' => Yii::$app->user->identity->staff->job->departament_id])->groupBy('batch.id')->all(), 'id', 'employed_at'),
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Выберите партию проб...',
                    'id' => 'batch-field-select'
                ],
                'pluginOptions' => [
                    'ajax' => [
                        'url' => Url::to(['batch/list', 'empty' => true]),
                        'dataType' => 'json',
                        'delay' => 200,
                        'data' => new JsExpression('function(params) {
                            return {term: params.term, page: params.page, limit: 20};
                        }'),
                        'processResults' => new JsExpression('function(data) {
                            return {results: data.results, pagination: { more: data.more }}
                        }'),
                    ],
                ]
            ]); ?>

            <div class="form-group">
                <?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Назад', Yii::$app->request->referrer, ['class' => 'btn btn-secondary']) ?>
            </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>