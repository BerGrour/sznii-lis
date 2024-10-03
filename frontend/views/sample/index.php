<?php

use common\models\Departament;
use common\models\Sample;
use kartik\daterange\DateRangePicker;
use kartik\helpers\Html;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\components\CustomActionColumn;
use yii\grid\GridView;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var common\models\SampleSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider query from Sample model */

$this->title = 'Пробы';
$this->params['breadcrumbs'][] = ['label' => 'Партии проб', 'url' => ['batch/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sample-index">

    <h1><?= $this->title ?></h1>

    <?php if (Yii::$app->user->can('batch/see')) { ?>
        <p class="buttons buttons-justify">
            <?php // Кнопка с созданием одной пробы? ?>
            <?= Html::a('Партии проб', ['batch/index'], ['class' => 'btn btn-primary button-justify-right']) ?>
        </p>
    <?php } ?>

    <?= GridView::widget([
        'filterModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['class' => 'grid_column-serial']
            ],
            [
                'attribute' => 'identificator',
                'format' => 'raw',
                'value' => function (Sample $model) {
                    return $model->getLinkOnView($model->identificator);
                }
            ],
            [
                'attribute' => 'batch_id',
                'value' => function (Sample $model) {
                    $batch = $model->batch;
                    return Yii::$app->formatter->asDatetime($batch->employed_at, 'medium');
                },
                'headerOptions' => ['class' => 'grid_column-date_range'],
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'convertFormat' => true,
                    'presetDropdown' => true,
                    'attribute' => 'batch_date',
                    'options' => ['placeholder' => 'Выберите диапазон'],
                    'language' => 'ru',
                    'pluginOptions' => [
                        'todayHighlight' => true,
                        'autoclose' => true,
                        'locale' => [
                            'format' => 'Y-m-d',
                            'separator' => ' | ',
                        ],
                    ]
                ])
            ],
            [
                'attribute' => 'laboratory',
                'format' => 'raw',
                'value' => function (Sample $model) {
                    return Html::encode($model->departament->title);
                },
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'laboratory',
                    'language' => 'ru',
                    'data' => ArrayHelper::map(Departament::find()->where(['role' => 'laboratory'])->all(), 'title', 'title'),
                    'value' => 'departament.title',
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'Выберите лабораторию'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'formatSelection' => new JsExpression('function (data) {
                            return data.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
                        }'),
                    ]
                    ]),
            ],
            [
                'attribute' => 'losted_at',
                'headerOptions' => ['class' => 'grid_column-date_range'],
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'convertFormat' => true,
                    'presetDropdown' => true,
                    'attribute' => 'losted_at',
                    'options' => ['placeholder' => 'Выберите диапазон'],
                    'language' => 'ru',
                    'pluginOptions' => [
                        'todayHighlight' => true,
                        'autoclose' => true,
                        'locale' => [
                            'format' => 'Y-m-d',
                            'separator' => ' | ',
                        ],
                    ]
                ])
            ],
            [
                'class' => CustomActionColumn::class,
                'urlCreator' => function ($action, Sample $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'template' => '{update}{delete}',
                'visible' => Yii::$app->user->can('sample/delete'),
            ],
        ],
        'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> проб',
        'emptyText' => 'Проб не найдено'
    ]); ?>

</div>