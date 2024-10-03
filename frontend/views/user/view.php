<?php

use common\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\User $model */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['user/index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= $this->title ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'template' => function ($attribute, $index, $widget) {
            if ($attribute['value']) {
                return "<tr><th>{$attribute['label']}</th><td>{$attribute['value']}</td></tr>";
            }
        },
        'attributes' => [
            'username',
            [
                'attribute' => 'role',
                'format' => 'raw',
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function (User $model) {
                    return $model->getStatusName();
                }
            ],
            [
                'attribute' => 'organization_id',
                'format' => 'raw',
                'value' => function (User $model) {
                    $organization = $model->organization;
                    if ($organization) {
                        return $organization->getLinkOnView(
                            Html::encode($organization->name),
                            title: $organization->name
                        );
                    }
                    return null;
                }
            ],
            [
                'attribute' => 'staff_id',
                'format' => 'raw',
                'value' => function (User $model) {
                    $staff = $model->staff;
                    if ($staff) {
                        return $staff->getLinkOnView(
                            Html::encode($staff->fio),
                            title: $staff->fio
                        );
                    }
                    return null;
                }
            ],
        ],
    ]); ?>

</div>