<?php

use common\models\Contract;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Contract $model */

$this->title = $model->getShortTitle();
$this->params['breadcrumbs'][] = ['label' => 'Организации', 'url' => ['organization/index']];
$this->params['breadcrumbs'][] = ['label' => $model->organization->getShortTitle(), 'url' => ['organization/view', 'id' => $model->organization_id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="contract-view">

    <h1><?= $this->title ?></h1>

    <?php if (Yii::$app->user->can('contract/create')) { ?>
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
            'number',
            [
                'attribute' => 'organization_id',
                'format' => 'raw',
                'value' => function (Contract $model) {
                    $organization = $model->organization;
                    return $organization->getLinkOnView(
                        Html::encode($organization->name),
                        title: $organization->name
                    );
                }
            ],
            'start_date',
            'end_date',
            'list_date',
        ],
    ]); ?>

</div>