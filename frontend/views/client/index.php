<?php

/** @var yii\web\View $this */

use common\models\Departament;
use common\models\Organization;
use common\models\Service;
use kartik\daterange\DateRangePicker;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\SampleSearch $servicesSearchModel */
/** @var yii\data\ActiveDataProvider $servicesDataProvider query from Service model  */
/** @var common\models\Organization $organization */

$this->title = $organization->getShortTitle();
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div>
    <h1><?= $this->title ?></h1>

    <?= DetailView::widget([
        'model' => $organization,
        'template' => function ($attribute, $index, $widget) {
            if ($attribute['value']) {
                return "<tr><th>{$attribute['label']}</th><td>{$attribute['value']}</td></tr>";
            }
        },
        'attributes' => [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function (Organization $model) {
                    return Html::encode($model->name);
                }
            ],
            [
                'label' => 'Договор',
                'format' => 'raw',
                'value' => function (Organization $model) {
                    return $model->getInfoAboutContract();
                }
            ],
        ]
    ]); ?>

    <div class="services-client">

        <?= GridView::widget([
            'filterModel' => $servicesSearchModel,
            'dataProvider' => $servicesDataProvider,
            'striped' => false,
            'hover' => true,
            'pjax' => false,
            'filterSelector' => 'input[name="ServiceSearch[batch_date]"]',
            'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
            'export' => [
                'showConfirmAlert' => false,
            ],
            'exportConfig' => ['txt' => [], 'xls' => [], 'csv' => []],
            'panel' => [
                'type' => 'default',
                'heading' => 'Партии проб и их исследования',
                'before' => '<div class="grid_column-date_range">'
                    . DateRangePicker::widget([
                        'model' => $servicesSearchModel,
                        'convertFormat' => true,
                        'presetDropdown' => true,
                        'attribute' => 'batch_date',
                        'options' => [
                            'placeholder' => 'Фильтр по датам партий...',
                            'id' => 'client-batch-date_range_picker',
                        ],
                        'language' => 'ru',
                        'pluginOptions' => [
                            'allowClear' => true,
                            'todayHighlight' => true,
                            'autoclose' => true,
                            'locale' => [
                                'format' => 'd.m.Y',
                                'separator' => ' | ',
                            ]
                        ],
                    ])
                    . '</div>'
            ],
            'columns' => [
                [
                    'class' => 'kartik\grid\SerialColumn',
                    'headerOptions' => ['class' => 'grid_column-serial']
                ],
                [
                    'attribute' => 'batch_date',
                    'format' => 'raw',
                    'value' => function (Service $model) {
                        $batch = $model->batch;
                        return 'Партия от ' . Yii::$app->formatter->asDateTime($batch->employed_at, 'php:d.m.Y H:i');
                    },
                    'group' => true,
                    'groupedRow' => true,
                    'groupOddCssClass' => 'kv-grouped-row',
                    'groupEvenCssClass' => 'kv-grouped-row',
                    // Фикс бага kartik/GridView, отображающий groupedRow поле при пустом результате
                    'visible' => $servicesDataProvider->getCount() == 0 ? false : true,
                    'groupFooter' => function ($model, $key, $index, $widget) {
                        $footer = [
                            'mergeColumns' => [[0, 3]],
                            'content' => [
                                0 => $model->summaryTitle,
                                4 => $model->summaryStatus,
                                5 => $model->summaryAmount,
                                6 => $model->summaryPrice,
                                7 => $model->summaryFiles,
                            ],
                            'contentOptions' => [
                                0 => ['style' => 'font-variant:small-caps'],
                            ],
                            'options' => [
                                'class' => 'info table-danger',
                                'style' => 'font-weight:bold;',
                            ],
                        ];
                        $this->registerJs("
                            $(function() {
                                $('[data-toggle=\"tooltip\"]').tooltip();
                            });
                        ");

                        return $footer;
                    }
                ],
                [
                    'attribute' => 'sample_type',
                    'format' => 'raw',
                    'value' => function (Service $model) {
                        $departament = $model->staff->job->departament;
                        if ($departament->phone) {
                            return "<span class=\"tooltip-custom\" data-toggle=\"tooltip\" title=\"{$departament->phone}\">{$departament->short_name}</span>";
                        }
                        return $departament->short_name;
                    },
                    'enableSorting' => false,
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter' => ArrayHelper::map(Departament::find()->where(['role' => 'laboratory'])->all(), 'id', 'short_name'),
                    'filterWidgetOptions' => [
                        'language' => 'ru',
                        'pluginOptions' => [
                            'allowClear' => true,
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'formatSelection' => new JsExpression('function (data) {
                                return data.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
                            }'),
                        ]
                    ],
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'placeholder' => 'Выберите вид проб'
                    ],
                    'headerOptions' => ['style' => 'width: 200px;'],
                    'group' => true,
                    'subGroupOf' => 1,
                ],
                [
                    'attribute' => 'research',
                    'format' => 'raw',
                    'value' => function (Service $model) {
                        return Html::encode($model->research);
                    },
                    'enableSorting' => false,
                ],
                [
                    'attribute' => 'statusClient',
                    'format' => 'raw',
                    'value' => function (Service $model) {
                        return $model->getStatus(true);
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter' => [
                        1 => Service::STATUS_CLIENT_RESEARCHING,
                        2 => Service::STATUS_CLIENT_ACTING,
                        3 => Service::STATUS_CLIENT_NOTPAY,
                        4 => Service::STATUS_CLIENT_WAITING,
                        5 => Service::STATUS_CLIENT_CHECKED,
                        6 => Service::STATUS_CLIENT_COMPLETED,
                    ],
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'placeholder' => '',
                    ],
                    'filterWidgetOptions' => [
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ],
                    'headerOptions' => ['style' => 'width: 170px;'],
                ],
                [
                    'attribute' => 'amount',
                    'format' => 'raw',
                    'label' => 'Количество<br> проб',
                    'value' => function (Service $model) {
                        if ($model->locked == 1) {
                            return $model->getActiveSamples()->count();
                        }
                        return '<span class="another-info-span">&asymp;'
                            . $model->getActiveSamples()->count()
                            . '</span>';
                    },
                    'mergeHeader' => true,
                    'encodeLabel' => false,
                    'filter' => false,
                    'headerOptions' => ['style' => 'width: 40px;'],
                ],
                [
                    'attribute' => 'sum',
                    'format' => ['raw'],
                    'value' => function (Service $model) {
                        return $model->getSum();
                    },
                    'filter' => false,
                    'enableSorting' => false,
                    'class' => 'kartik\grid\FormulaColumn',
                    'mergeHeader' => true,
                ],
                [
                    'label' => 'Файлы',
                    'format' => 'raw',
                    'value' => function (Service $model) {
                        return $model->getIconFileResults(true);
                    },
                    'headerOptions' => ['class' => 'grid_column-files skip-export'],
                    'contentOptions' => ['class' => 'skip-export'],

                    'mergeHeader' => true,
                ],
            ]
        ]) ?>
    </div>
</div>