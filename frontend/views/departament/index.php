<?php

use common\models\Departament;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\CustomActionColumn;
use common\models\User;
use yii\grid\GridView;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var common\models\DepartamentSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider query from Departament model */

$this->title = 'Отделы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="departament-index">

    <h1><?= $this->title ?></h1>

    <?php if (Yii::$app->user->can('departament/create')) { ?>
        <p>
            <?= Html::a('Создать отдел', ['create'], ['class' => 'btn btn-success']) ?>
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
                'attribute' => 'title',
                'format' => 'raw',
                'value' => function (Departament $model) {
                    return $model->getLinkOnView(
                        Html::encode($model->title),
                        title: $model->title
                    );
                }
            ],
            [
                'attribute' => 'short_name',
                'format' => 'raw',
                'value' => function (Departament $model) {
                    return Html::encode($model->short_name);
                }
            ],
            [
                'attribute' => 'role',
                'format' => 'raw',
                'value' => function (Departament $model) {
                    return User::getRoleInfo($model->role);
                },
                'visible' => Yii::$app->user->can('manageRoles'),
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'role',
                    'language' => 'ru',
                    'data' => ArrayHelper::map(User::getListRoles(), 'name', 'description'),
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'Выберите роль'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'formatSelection' => new JsExpression('function (data) {
                            return data.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
                        }')
                    ]
                ])
            ],
            [
                'class' => CustomActionColumn::class,
                'urlCreator' => function ($action, Departament $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'template' => '{update}{delete}',
                'visible' => Yii::$app->user->can('departament/delete'),
            ],
        ],
        'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> отделов',
        'emptyText' => 'Отделов не найдено'
    ]); ?>


</div>