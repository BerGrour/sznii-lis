<?php

use common\models\Job;
use common\models\Staff;
use kartik\daterange\DateRangePicker;
use common\components\CustomActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\StaffSearch $staffSearchModel */
/** @var yii\data\ActiveDataProvider $staffDataProvider query from Staff model */
/** @var common\models\Job $model */

$this->title = $model->getShortTitle();
$this->params['breadcrumbs'][] = ['label' => 'Отделы', 'url' => ['departament/index']];
$this->params['breadcrumbs'][] = ['label' => $model->departament->getShortTitle(), 'url' => ['departament/view', 'id' => $model->departament_id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="job-view">

    <h1><?= $this->title ?></h1>

    <?php if (Yii::$app->user->can('job/create')) { ?>
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
        'template' => function ($attribute, $index, $widget) {
            if ($attribute['value']) {
                return "<tr><th>{$attribute['label']}</th><td>{$attribute['value']}</td></tr>";
            }
        },
        'attributes' => [
            [
                'attribute' => 'title',
                'format' => 'raw',
                'value' => function (Job $model) {
                    return Html::encode($model->title);
                }
            ],
            [
                'attribute' => 'departament_id',
                'format' => 'raw',
                'value' => function (Job $model) {
                    $departament = $model->departament;
                    return $departament->getLinkOnView(
                        Html::encode($departament->title),
                        title: $departament->title
                    );
                }
            ]
        ],
    ]) ?>

    <?php if (Yii::$app->user->can('staff/see')) { ?>
        <h2>Сотрудники:</h2>

        <div class="staffs">
            <?php if (Yii::$app->user->can('staff/create')) { ?>
                <p>
                    <?= Html::a('Создать сотрудника', ['staff/create', 'job_id' => $model->id], ['class' => 'btn btn-success', 'id' => 'create_object']) ?>
                </p>
            <?php } ?>

            <?= GridView::widget([
                'filterModel' => $staffSearchModel,
                'dataProvider' => $staffDataProvider,
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
                        'attribute' => 'employ_date',
                        'headerOptions' => ['class' => 'grid_column-date_range'],
                        'filter' => DateRangePicker::widget([
                            'model' => $staffSearchModel,
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
                        'label' => 'Учетная запись',
                        'visible' => Yii::$app->user->can('user/see'),
                        'format' => 'raw',
                        'value' => function (Staff $model) {
                            $user = $model->user;
                            if ($user) {
                                return $user->username;
                            }
                            return '<strong style="color:red;">Не привязано</strong>';
                        }
                    ],
                    'phone',
                    [
                        'class' => CustomActionColumn::class,
                        'urlCreator' => function ($action, Staff $model, $key, $index, $column) {
                            return Url::toRoute(["staff/{$action}", 'id' => $model->id]);
                        },
                        'template' => '{update}{delete}',
                        'visible' => Yii::$app->user->can('staff/delete'),
                    ],
                ],
                'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> сотрудников',
                'emptyText' => 'Сотрудников не найдено'
            ]); ?>
        </div>
    <?php } ?>

</div>