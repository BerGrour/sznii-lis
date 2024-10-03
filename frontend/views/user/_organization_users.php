<?php

use common\models\User;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\UserSearch $filterModel */
/** @var yii\data\ActiveDataProvider $dataProvider query from User model with organization only */
?>

<?= GridView::widget([
    'filterModel' => $filterModel,
    'dataProvider' => $dataProvider,
    'id' => 'gridUserOrganization',
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['class' => 'grid_column-serial']
        ],
        [
            'attribute' => 'username',
            'format' => 'raw',
            'value' => function (User $model) {
                return $model->getLinkOnView($model->username);
            }
        ],
        [
            'attribute' => 'organization_name',
            'label' => 'Организация',
            'format' => 'raw',
            'value' => function (User $model) {
                $organization = $model->organization;
                return $organization->getLinkOnView(
                    Html::encode($organization->name),
                    title: $organization->name
                );
            }
        ],
        [
            'attribute' => 'status',
            'format' => 'raw',
            'value' => function (User $model) {
                return $model->getStatusName();
            },
            'filter' => [
                0 => User::STATUS_DELETED_NAME,
                9 => User::STATUS_INACTIVE_NAME,
                10 => User::STATUS_ACTIVE_NAME
            ],
            'filterInputOptions' => [
                'prompt' => '-',
                'id' => 'orgsearch-status',
                'class' => 'form-control'
            ]
        ]
    ],
    'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> пользователей',
    'emptyText' => 'Пользователей не найдено'
]); ?>