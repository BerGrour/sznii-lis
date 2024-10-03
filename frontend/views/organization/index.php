<?php

use common\models\Organization;
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\CustomActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\OrganizationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider query from Organization model */

$this->title = 'Организации';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organization-index">

    <h1><?= $this->title ?></h1>

    <?php if (Yii::$app->user->can('organization/create')) { ?>
        <p>
            <?= Html::a('Создать организацию', ['create'], ['class' => 'btn btn-success']) ?>
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
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function (Organization $model) {
                    return $model->getLinkOnView(
                        Html::encode($model->name),
                        title: $model->name
                    );
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
                'attribute' => 'director',
                'format' => 'raw',
                'value' => function (Organization $model) {
                    return Html::encode($model->director);
                }
            ],
            [
                'attribute' => 'active_contract',
                'format' => 'raw',
                'value' => function (Organization $model) {
                    $contract = $model->getActiveContract();
                    if ($contract) {
                        return $contract->getShortTitle(true)
                            . ' <span class="another-info-span">('
                            . $model->getInfoAboutContract() . ")</span>";
                    }
                    return '<span class="warning-status">Действующего договора нет<span>';
                },
                'filterInputOptions' => ['class' => 'form-control without-arrows', 'type' => 'number']
            ],
            [
                'label' => 'Учетная запись',
                'visible' => Yii::$app->user->can('user/see'),
                'format' => 'raw',
                'value' => function (Organization $model) {
                    $user = $model->user;
                    if ($user) {
                        return $user->username;
                    }
                    return '<span class="warning-status">Не привязано</span>';
                }
            ],
            [
                'class' => CustomActionColumn::class,
                'urlCreator' => function ($action, Organization $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'template' => '{update}{delete}',
                'visible' => Yii::$app->user->can('organization/delete'),
            ],
        ],
        'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> организаций',
        'emptyText' => 'Организаций не найдено'
    ]); ?>


</div>