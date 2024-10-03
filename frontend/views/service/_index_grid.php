<?php

use common\models\Departament;
use common\models\Service;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;
use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var common\models\ServiceSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider query from Service model */

?>

<?= GridView::widget([
    'filterModel' => $searchModel,
    'dataProvider' => $dataProvider,
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
        'heading' => 'Исследования',
        'before' => '<div class="grid_column-date_range">'
            . DateRangePicker::widget([
                'model' => $searchModel,
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
                $content = 'Партия от ' . Yii::$app->formatter->asDateTime($batch->employed_at, 'php:d.m.Y H:i');
                if ($batch->payment) {
                    $content .= " ({$batch->payment->getShortTitle(true)})";
                }
                return $content;
            },
            'group' => true,
            'groupedRow' => true,
            'groupOddCssClass' => 'kv-grouped-row',
            'groupEvenCssClass' => 'kv-grouped-row',
            // Фикс бага kartik/GridView, отображающий groupedRow поле при пустом результате
            'visible' => $dataProvider->getCount() == 0 ? false : true,
        ],
        [
            'attribute' => 'research',
            'format' => 'raw',
            'value' => function (Service $model) {
                return $model->getLinkOnView(
                    Html::encode($model->research),
                    title: $model->research
                );
            }
        ],
        [
            'attribute' => 'started_at',
            'headerOptions' => ['class' => 'grid_column-date'],
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'attribute' => 'started_at',
                'options' => ['placeholder' => ''],
                'language' => 'ru',
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ])
        ],
        [
            'attribute' => 'laboratory',
            'format' => 'raw',
            'headerOptions' => ['class' => 'grid_column-laboratory'],
            'value' => function (Service $model) {
                $laboratory = $model->staff->job->departament;
                return Html::encode($laboratory->title);
            },
            'filter' => Select2::widget([
                'model' => $searchModel,
                'attribute' => 'laboratory',
                'language' => 'ru',
                'data' => ArrayHelper::map(Departament::find()->where(['role' => 'laboratory'])->all(), 'id', 'title'),
                'value' => 'departament.title',
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => ''
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'formatSelection' => new JsExpression('function (data) {
                        return data.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
                    }'),
                ]
            ]),
            'visible' => Yii::$app->user->can('departament/see'),
        ],
        [
            'attribute' => 'staff_name',
            'format' => 'raw',
            'value' => function (Service $model) {
                $staff = $model->staff;
                return Html::encode($staff->fio);
            }
        ],
        [
            'attribute' => 'completed_at',
            'headerOptions' => ['class' => 'grid_column-date'],
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'attribute' => 'completed_at',
                'options' => ['placeholder' => ''],
                'language' => 'ru',
                'pluginOptions' => [
                    'todayHighlight' => true,
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ]
            ])
        ],
        [
            'attribute' => 'status',
            'format' => 'raw',
            'filter' => Select2::widget([
                'model' => $searchModel,
                'attribute' => 'status',
                'language' => 'ru',
                'data' => [
                    0 => Service::STATUS_EMPTY,
                    1 => Service::STATUS_LATE,
                    2 => Service::STATUS_PROCESS,
                    3 => Service::STATUS_NOTPAY,
                    4 => Service::STATUS_COMPLETE
                ],
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => '',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ]
            ]),
            'headerOptions' => ['style' => 'min-width: 120px;']
        ],
    ],
    'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> исследований',
    'emptyText' => 'Исследований не найдено'
]); ?>