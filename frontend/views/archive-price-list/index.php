<?php

use common\models\ArchivePriceList;
use common\models\Departament;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider query from ArchivePrice model */
/** @var common\models\ArchivePriceListSearch $searchModel */

$this->title = 'Архив изменений цен на исследования';
$this->params['breadcrumbs'][] = ['label' => 'Каталог исследований', 'url' => ['price-list/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="archive-price-list-index">

    <h1><?= $this->title ?></h1>

    <p class="buttons buttons-justify">
        <?= Html::a('Каталог', ['price-list/index'], ['class' => 'btn btn-primary button-justify-right']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['class' => 'grid_column-serial']
            ],
            [
                'attribute' => 'research_name',
                'format' => 'raw',
                'value' => function (ArchivePriceList $model) {
                    $research = $model->research;
                    return $research->getLinkOnView(
                        Html::encode($research->research),
                        title: $research->research
                    );
                }
            ],
            [
                'attribute' => 'departament_name',
                'format' => 'raw',
                'value' => function (ArchivePriceList $model) {
                    $departament = $model->research->departament;
                    return $departament->getLinkOnView(
                        Html::encode($departament->title),
                        title: $departament->title
                    );
                },
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'departament_name',
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
                'visible' => Yii::$app->user->can('departament/see'),
            ],
            [
                'attribute' => 'price',
                'format' => ['decimal', 2],
                'filterInputOptions' => ['class' => 'form-control without-arrows', 'type' => 'number']
            ],
            [
                'attribute' => 'updated_at',
                'headerOptions' => ['class' => 'grid_column-date_range'],
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
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
        ],
    ]); ?>


</div>