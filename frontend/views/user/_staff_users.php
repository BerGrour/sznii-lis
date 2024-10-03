<?php

use common\models\User;
use kartik\select2\Select2;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var common\models\UserSearch $filterModel */
/** @var yii\data\ActiveDataProvider $dataProvider query from User model with staff only */
?>

<?= GridView::widget([
    'filterModel' => $filterModel,
    'dataProvider' => $dataProvider,
    'id' => 'gridUserStaff',
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
            'attribute' => 'staff_name',
            'label' => 'Сотрудник',
            'format' => 'raw',
            'value' => function (User $model) {
                $staff = $model->staff;
                return $staff->getLinkOnView(
                    Html::encode($staff->fio),
                    title: $staff->fio
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
                'id' => 'usersearch-status',
                'class' => 'form-control'
            ]
        ],
        [
            'attribute' => 'role',
            'format' => 'raw',
            'filter' => Select2::widget([
                'model' => $filterModel,
                'attribute' => 'role',
                'language' => 'ru',
                'data' => ArrayHelper::map(User::getListRoles(), 'description', 'description'),
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
        ]
    ],
    'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> пользователей',
    'emptyText' => 'Пользователей не найдено'
]); ?>