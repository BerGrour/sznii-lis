<?php

use common\models\Sample;
use kartik\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\Service $service */
/** @var yii\data\ActiveDataProvider $samplesBatchProvider данные о пробах из партии НЕ входящих в исследование */
/** @var yii\data\ActiveDataProvider $samplesServiceProvider данные о пробах входящих в исследование*/

$this->title = 'Изменение состава проб для: ' . $service->getShortTitle();
$this->params['breadcrumbs'][] = ['label' => 'Исследования', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $service->getShortTitle(), 'url' => ['view', 'id' => $service->id]];
$this->params['breadcrumbs'][] = 'Изменение состава проб';
?>
<div class="service-update">

    <h1><?= $this->title ?></h1>

    <?php Pjax::begin(); ?>
        <div class="service-form">

            <?= $form = Html::beginForm(['samples-edit', 'id' => $service->id], 'post', ['class' => 'service-samples-edit']); ?>

                <div class="form-group form-group-grid-view">
                    <h5 class="control-label">Пробы входящие в исследование</h5>
                    <?= GridView::widget([
                        'id' => 'samples-from-service',
                        'dataProvider' => $samplesServiceProvider,
                        'columns' => [
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'selection_in',
                                'headerOptions' => ['class' => 'grid_column-checkbox']
                            ],
                            [
                                'attribute' => 'identificator',
                                'format' => 'raw',
                                'value' => function (Sample $model) {
                                    $result = $model->identificator;
                                    if (!empty($model->losted_at)) {
                                        $result .= ' <span class="warning-status">Потеряна</span>';
                                    }
                                    return $result;
                                }
                            ],
                            [
                                'attribute' => 'uses_count',
                                'label' => 'Кол-во<br>использований',
                                'encodeLabel' => false,
                                'format' => 'raw',
                                'value' => function (Sample $model) {
                                    return count($model->sampleServices);
                                }
                            ]
                        ],
                        'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> проб исследования',
                        'emptyText' => 'Проб в исследовании не найдено'
                    ]); ?>
                </div>

                <div class="form-group form-group-button">
                    <?= Html::submitButton('Провести смену', ['class' => 'btn btn-success']) ?>
                    <?= Html::a('Завершить изменение', ['view', 'id' => $service->id], ['class' => 'btn btn-secondary', 'data-pjax' => 0]) ?>
                </div>

                <div class="form-group form-group-grid-view">
                    <h5 class="control-label">Другие пробы из партии</h5>
                    <?= GridView::widget([
                        'id' => 'other-samples',
                        'dataProvider' => $samplesBatchProvider,
                        'columns' => [
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'name' => 'selection_out',
                                'headerOptions' => ['class' => 'grid_column-checkbox']
                            ],
                            [
                                'attribute' => 'identificator',
                                'format' => 'raw',
                                'value' => function (Sample $model) {
                                    $result = $model->identificator;
                                    if (!empty($model->losted_at)) {
                                        $result .= ' <span class="warning-status">Потеряна</span>';
                                    }
                                    return $result;
                                }
                            ],
                            [
                                'attribute' => 'uses_count',
                                'label' => 'Кол-во<br>использований',
                                'encodeLabel' => false,
                                'format' => 'raw',
                                'value' => function (Sample $model) {
                                    return count($model->sampleServices);
                                }
                            ]
                        ],
                        'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> проб партии',
                        'emptyText' => 'Других проб в партии не найдено'
                    ]); ?>
                </div>

            <?= Html::endForm(); ?>
        </div>
    <?php Pjax::end(); ?>

</div>