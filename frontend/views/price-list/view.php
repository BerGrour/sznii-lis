<?php

use common\models\PriceList;
use kartik\daterange\DateRangePicker;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\ArchivePriceListSearch $archiveSearchModel */
/** @var yii\data\ActiveDataProvider $archiveDataProvider query from ArchivePriceList model */
/** @var common\models\PriceList $model */

$this->title = $model->getShortTitle();
$this->params['breadcrumbs'][] = ['label' => 'Каталог исследований', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="price-list-view">

    <h1><?= $this->title ?></h1>

    <p>
        <?php if (Yii::$app->user->can('price_list/update')) { ?>
            <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php } ?>
        <?php if (Yii::$app->user->can('price_list/delete')) { ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены, что хотите удалить этот элемент?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php } ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'template' => function ($attribute, $index, $widget) {
            if ($attribute['value']) {
                return "<tr><th>{$attribute['label']}</th><td>{$attribute['value']}</td></tr>";
            }
        },
        'attributes' => [
            [
                'attribute' => 'departament_id',
                'format' => 'raw',
                'value' => function (PriceList $model) {
                    $departament = $model->departament;
                    return $departament->getLinkOnView(
                        Html::encode($departament->title),
                        title: $departament->title
                    );
                }
            ],
            [
                'attribute' => 'research',
                'format' => 'raw',
                'value' => function (PriceList $model) {
                    return Html::encode($model->research);
                }
            ],
            [
                'attribute' => 'price',
                'format' => ['decimal', 2],
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function (PriceList $model) {
                    return $model->getStatusName();
                }
            ],
            'period'
        ],
    ]) ?>

    <?php if (Yii::$app->user->can('archive_price_list/see')) { ?>
        <h2>История изменения цен:</h2>

        <div class="archivePriceList">

            <?= GridView::widget([
                'filterModel' => $archiveSearchModel,
                'dataProvider' => $archiveDataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['class' => 'grid_column-serial']
                    ],
                    [
                        'attribute' => 'updated_at',
                        'headerOptions' => ['class' => 'grid_column-date_range'],
                        'filter' => DateRangePicker::widget([
                            'model' => $archiveSearchModel,
                            'convertFormat' => true,
                            'presetDropdown' => true,
                            'attribute' => 'updated_at',
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
                        'attribute' => 'price',
                        'format' => ['decimal', 2],
                        'filterInputOptions' => ['class' => 'form-control without-arrows', 'type' => 'number']
                    ]
                ],
                'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> изменений',
                'emptyText' => 'Изменений не найдено'
            ]); ?>
        </div>
    <?php } ?>

</div>