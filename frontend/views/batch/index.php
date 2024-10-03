<?php

use common\models\Batch;
use common\models\Contract;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use common\components\CustomActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var common\models\BatchSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider query from Batch model */

$this->title = 'Партии проб';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="batch-index">

    <h1><?= $this->title ?></h1>

    <?php if (Yii::$app->user->can('batch/create')) { ?>
        <p class="buttons buttons-justify">
            <?= Html::a('Прием проб', ['create'], ['class' => 'btn btn-success']); ?>

            <?php if (Yii::$app->user->can('sample/see')) { ?>
                <?= Html::a('Все пробы', ['sample/index'], ['class' => 'btn btn-primary button-justify-right']) ?>
            <?php } ?>
        </p>
    <?php } ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['class' => 'grid_column-serial']
            ],
            [
                'attribute' => 'employed_at',
                'format' => 'raw',
                'value' => function (Batch $model) {
                    return $model->getLinkOnView(
                        Yii::$app->formatter->asDatetime($model->employed_at, 'medium')
                    );
                },
                'headerOptions' => ['class' => 'grid_column-date_range'],
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'convertFormat' => true,
                    'presetDropdown' => true,
                    'attribute' => 'employed_at',
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
                'attribute' => 'organization_name',
                'format' => 'raw',
                'value' => function (Batch $model) {
                    return $model->contract->getTitleActiveOrg();
                },
                'visible' => Yii::$app->user->can('organization/see'),
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'organization_name',
                    'language' => 'ru',
                    'data' => ArrayHelper::map(Contract::find()->joinWith('organization')->orderBy(['organization.name' => SORT_ASC, 'number' => SORT_DESC])->all(), 'id', 'titleActiveOrg'),
                    'value' => 'organization.name',
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'Выберите организацию'
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
                'attribute' => 'amount',
                'format' => 'raw',
                'value' => function (Batch $model) {
                    return $model->getSamples()->count();
                },
                'visible' => Yii::$app->user->can('organization/see'),
                'headerOptions' => ['style' => 'width:3%']
            ],
            [
                'class' => CustomActionColumn::class,
                'urlCreator' => function ($action, Batch $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'template' => '{update}{delete}',
                'visible' => Yii::$app->user->can('batch/delete'),
            ],
        ],
        'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> партий проб',
        'emptyText' => 'Партий проб не найдено'
    ]); ?>


</div>