<?php

use common\models\Batch;
use common\models\Departament;
use common\models\Sample;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use common\components\CustomActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\SampleSearch $sampleSearchModel */
/** @var yii\data\ActiveDataProvider $sampleDataProvider query from Sample model */
/** @var common\models\Batch $model */

$this->title = $model->getShortTitle();
$this->params['breadcrumbs'][] = ['label' => 'Партии проб', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="batch-view">

    <h1><?= $this->title ?></h1>

    <?php if (Yii::$app->user->can('batch/update')) { ?>
        <p>
            <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены, что хотите удалить этот элемент?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    <?php } ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'employed_at',
            [
                'label' => 'Организация',
                'format' => 'raw',
                'value' => function (Batch $model) {
                    $organization = $model->contract->organization;
                    return $organization->getLinkOnView(
                        Html::encode($organization->name),
                        '_blank',
                        title: $organization->name
                    );
                }
            ],
            [
                'label' => 'Договор',
                'format' => 'raw',
                'value' => function (Batch $model) {
                    $contract = $model->contract;
                    return $contract->getLinkOnView("Договор № {$contract->number}", '_blank');
                }
            ],
            [
                'attribute' => 'staff_id',
                'format' => 'raw',
                'value' => function (Batch $model) {
                    $staff = $model->staff;
                    return $staff->getLinkOnView(
                        Html::encode($staff->fio),
                        '_blank',
                        title: $staff->fio
                    );
                }
            ],
            [
                'attribute' => 'payment_id',
                'format' => 'raw',
                'value' => function (Batch $model) {
                    $payment = $model->payment;
                    if ($payment) {
                        return $payment->getLinkOnView("Акт № {$payment->act_num}");
                    }
                }
            ]
        ],
    ]) ?>

    <?php if (Yii::$app->user->can('sample/see')) { ?>
        <h2>Пробы:</h2>

        <?= $form = Html::beginForm(['samples-edit', 'id' => 'samplesFromBatchForm'], 'post'); ?>

        <p>
            <?php if (Yii::$app->user->can('service/create')) { ?>
                <?= Html::a('Зарегистрировать исследования', ['service/multi-select'], [
                    'class' => 'btn btn-success',
                    'id' => 'samplesServiceBulkCreate',
                    'data' => ['method' => 'post']
                ]); ?>
            <?php } ?>
            <?php if (Yii::$app->user->can('sample/delete')) { ?>
                <?= Html::a('Удалить выбранные пробы', ['bulk-delete-samples'], [
                        'class' => 'btn btn-danger',
                        'id' => 'samplesBulkDelete',
                        'data' => [
                            'confirm' => 'Вы уверены, что хотите удалить выбранные пробы?',
                            'method' => 'post',
                        ],
                    ]) ?>
            <?php } ?>
        </p>

        <div class="samples">
            <?= GridView::widget([
                'options' => [
                    'id' => 'gridview_samples'
                ],
                'filterModel' => $sampleSearchModel,
                'dataProvider' => $sampleDataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'name' => 'selection_samples[]',
                        'headerOptions' => ['class' => 'grid_column-checkbox']
                    ],
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['class' => 'grid_column-serial']
                    ],
                    [
                        'attribute' => 'identificator',
                        'format' => 'raw',
                        'value' => function (Sample $model) {
                            $content = $model->identificator;
                            $another = null;
                            if (!empty($model->losted_at)) {
                                $another = $model->getStatusLost($model->id);
                            }
                            return $model->getLinkOnView($content) . $another;
                        }
                    ],
                    [
                        'attribute' => 'laboratory',
                        'format' => 'raw',
                        'value' => function (Sample $model) {
                            return Html::encode($model->departament->title);
                        },
                        'filter' => Select2::widget([
                            'model' => $sampleSearchModel,
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
                        ])
                    ],
                    [
                        'attribute' => 'losted_at',
                        'headerOptions' => ['class' => 'grid_column-date_range'],
                        'filter' => DateRangePicker::widget([
                            'model' => $sampleSearchModel,
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
                        'label' => 'Кол-во<br>использований',
                        'encodeLabel' => false,
                        'format' => 'raw',
                        'value' => function (Sample $model) {
                            return count($model->sampleServices);
                        }
                    ],
                    [
                        'class' => CustomActionColumn::class,
                        'urlCreator' => function ($action, Sample $model, $key, $index, $column) {
                            return Url::toRoute(["sample/{$action}", 'id' => $model->id]);
                        },
                        'template' => '{update}{delete}',
                        'visible' => Yii::$app->user->can('sample/delete'),
                    ],
                ],
                'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> проб',
                'emptyText' => 'Проб не найдено'
            ]); ?>
        </div>

        <?= Html::endForm(); ?>
    <?php } ?>
</div>