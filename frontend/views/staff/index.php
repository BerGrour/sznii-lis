<?php

use common\models\Departament;
use common\models\Job;
use common\models\Staff;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\components\CustomActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var common\models\StaffSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider query from Staff model */

$this->title = 'Сотрудники';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="staff-index">

    <h1><?= $this->title ?></h1>

    <?php if (Yii::$app->user->can('staff/create')) { ?>
        <p>
            <?= Html::a('Создать сотрудника', ['create-adaptive'], ['class' => 'btn btn-success']) ?>
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
                'attribute' => 'fio',
                'format' => 'raw',
                'value' => function (Staff $model) {
                    return $model->getLinkOnView(
                        Html::encode($model->fio),
                        title: $model->fio
                    );
                }
            ],
            [
                'attribute' => 'job_name',
                'format' => 'raw',
                'value' => function (Staff $model) {
                    $job = $model->job;
                    return $job->getLinkOnView(
                        Html::encode($job->title),
                        title: $job->title
                    );
                },
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'job_name',
                    'language' => 'ru',
                    'data' => ArrayHelper::map(Job::find()->all(), 'title', 'title'),
                    'value' => 'job.name',
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'Выберите должность'
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
                'attribute' => 'departament_name',
                'format' => 'raw',
                'value' => function (Staff $model) {
                    $departament = $model->job->departament;
                    return $departament->getLinkOnView(
                        Html::encode($departament->title),
                        title: $departament->title
                    );
                },
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'departament_name',
                    'language' => 'ru',
                    'data' => ArrayHelper::map(Departament::find()->all(), 'title', 'title'),
                    'value' => 'departament.name',
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'Выберите отдел'
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
                'attribute' => 'employ_date',
                'headerOptions' => ['class' => 'grid_column-date_range'],
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'convertFormat' => true,
                    'presetDropdown' => true,
                    'attribute' => 'employ_date',
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
                'attribute' => 'leave_date',
                'headerOptions' => ['class' => 'grid_column-date_range'],
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'convertFormat' => true,
                    'presetDropdown' => true,
                    'attribute' => 'leave_date',
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
                'urlCreator' => function ($action, Staff $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'template' => '{update}{delete}',
                'visible' => Yii::$app->user->can('staff/delete'),
            ],
        ],
    ]); ?>


</div>