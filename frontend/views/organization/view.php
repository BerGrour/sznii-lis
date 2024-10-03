<?php

use common\models\Contract;
use common\models\Organization;
use kartik\date\DatePicker;
use common\components\CustomActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\ContractSearch $contractSearchModel */
/** @var yii\data\ActiveDataProvider $contractDataProvider query from Contract model */
/** @var common\models\Organization $model */

$this->title = $model->getShortTitle();
$this->params['breadcrumbs'][] = ['label' => 'Организации', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="organization-view">

    <h1><?= $this->title; ?></h1>

    <?php if (Yii::$app->user->can('organization/create')) { ?>
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
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function (Organization $model) {
                    return Html::encode($model->name);
                }
            ],
            [
                'label' => 'Учетная запись',
                'format' => 'raw',
                'visible' => Yii::$app->user->can('user/see'),
                'value' => function (Organization $model) {
                    $user = $model->user;
                    if ($user) {
                        return $user->getLinkOnView($user->username);
                    }
                    $content = '<strong style="color:red;">Не привязано</strong>';
                    if (Yii::$app->user->can('user/create')) {
                        $content = Html::a(
                            'Создать пользователя',
                            ['/site/signup'],
                            ['class' => 'btn btn-success btn-sm', 'target' => '_blank']
                        );
                    }
                    return $content;
                }
            ],
            [
                'attribute' => 'inn',
                'format' => 'raw',
                'value' => function (Organization $model) {
                    return Html::encode($model->inn);
                }
            ],
            [
                'attribute' => 'address',
                'format' => 'raw',
                'value' => function (Organization $model) {
                    return Html::encode($model->address);
                }
            ],
            'phone',
            'email:email',
            [
                'attribute' => 'director',
                'format' => 'raw',
                'value' => function (Organization $model) {
                    return Html::encode($model->director);
                }
            ],
            [
                'attribute' => 'comment',
                'format' => 'raw',
                'value' => function (Organization $model) {
                    return Html::encode($model->comment);
                }
            ],
        ],
    ]) ?>

    <?php if (Yii::$app->user->can('contract/see')) { ?>
        <h2>Договора:</h2>

        <div class="contracts">
            <?php if (Yii::$app->user->can('contract/create')) { ?>
                <p>
                    <?= Html::a('Создать договор', ['contract/create', 'organization_id' => $model->id], ['class' => 'btn btn-success', 'id' => 'create_object']) ?>
                </p>
            <?php } ?>

            <?= GridView::widget([
                'filterModel' => $contractSearchModel,
                'dataProvider' => $contractDataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['class' => 'grid_column-serial']
                    ],
                    [
                        'attribute' => 'number',
                        'format' => 'raw',
                        'value' => function (Contract $model) {
                            return $model->getLinkOnView("Договор № {$model->number}");
                        },
                        'filterInputOptions' => ['class' => 'form-control without-arrows', 'type' => 'number']
                    ],
                    [
                        'attribute' => 'start_date',
                        'headerOptions' => ['class' => 'grid_column-date_range'],
                        'filter' => DatePicker::widget([
                            'model' => $contractSearchModel,
                            'type' => DatePicker::TYPE_COMPONENT_APPEND,
                            'attribute' => 'start_date',
                            'options' => ['placeholder' => 'Начальная дата...'],
                            'language' => 'ru',
                            'pluginOptions' => [
                                'todayHighlight' => true,
                                'format' => 'yyyy-mm-dd',
                                'autoclose' => true,
                            ]
                        ])
                    ],
                    [
                        'attribute' => 'end_date',
                        'headerOptions' => ['class' => 'grid_column-date_range'],
                        'filter' => DatePicker::widget([
                            'model' => $contractSearchModel,
                            'type' => DatePicker::TYPE_COMPONENT_APPEND,
                            'attribute' => 'end_date',
                            'options' => ['placeholder' => 'Конечная дата...'],
                            'language' => 'ru',
                            'pluginOptions' => [
                                'todayHighlight' => true,
                                'format' => 'yyyy-mm-dd',
                                'autoclose' => true,
                            ]
                        ])
                    ],
                    [
                        'class' => CustomActionColumn::class,
                        'urlCreator' => function ($action, Contract $model, $key, $index, $column) {
                            return Url::toRoute(["contract/{$action}", 'id' => $model->id]);
                        },
                        'template' => '{update}{delete}',
                        'visible' => Yii::$app->user->can('contract/delete'),
                    ],
                ],
                'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> договоров',
                'emptyText' => 'Договоров не найдено'
            ]); ?>
        </div>
    <?php } ?>

</div>