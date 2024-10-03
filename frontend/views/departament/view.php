<?php

use common\models\Departament;
use common\models\Job;
use common\components\CustomActionColumn;
use common\models\User;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\JobSearch $jobSearchModel */
/** @var yii\data\ActiveDataProvider $jobDataProvider query from Job model */
/** @var common\models\Departament $model */

$this->title = $model->getShortTitle();
$this->params['breadcrumbs'][] = ['label' => 'Отделы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="departament-view">

    <h1><?= $this->title ?></h1>

    <?php if (Yii::$app->user->can('departament/update')) { ?>
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
                'value' => function (Departament $model) {
                    return Html::encode($model->title);
                }
            ],
            'phone',
            [
                'attribute' => 'roleName',
                'format' => 'raw',
                'value' => function (Departament $model) {
                    return User::getRoleInfo($model->role);
                },
                'visible' => Yii::$app->user->can('manageRoles')
            ],
            [
                'attribute' => 'short_name',
                'format' => 'raw',
                'value' => function (Departament $model) {
                    return Html::encode($model->short_name);
                }
            ],
            'abbreviation',
            'periodName'
        ],
    ]) ?>

    <?php if (Yii::$app->user->can('job/see')) { ?>
        <h2>Должности отдела:</h2>

        <div class="jobs">
            <?php if (Yii::$app->user->can('job/create')) { ?>
                <p>
                    <?= Html::a('Создать должность', ['job/create', 'departament_id' => $model->id], ['class' => 'btn btn-success', 'id' => 'create_object']) ?>
                </p>
            <?php } ?>

            <?= GridView::widget([
                'filterModel' => $jobSearchModel,
                'dataProvider' => $jobDataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['class' => 'grid_column-serial']
                    ],
                    [
                        'attribute' => 'title',
                        'format' => 'raw',
                        'value' => function (Job $model) {
                            return $model->getLinkOnView(
                                Html::encode($model->title),
                                title: $model->title
                            );
                        }
                    ],
                    [
                        'class' => CustomActionColumn::class,
                        'urlCreator' => function ($action, Job $model, $key, $index, $column) {
                            return Url::toRoute(['job/' . $action, 'id' => $model->id]);
                        },
                        'template' => '{update}{delete}',
                        'visible' => Yii::$app->user->can('job/delete'),
                    ],
                ],
                'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> должностей',
                'emptyText' => 'Должностей не найдено'
            ]); ?>
        </div>
    <?php } ?>

</div>