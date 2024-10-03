<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/** @var yii\web\View $this */
/** @var common\models\ServiceSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider query from Service model */
/** @var yii\data\ActiveDataProvider $alertSamplesProvider уведомления о приходе партий*/

$this->title = 'Исследования';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-index">

    <?php if (Yii::$app->user->can('service/create') && Yii::$app->user->identity->staff->job->departament->role == 'laboratory') { ?>
        <?= ListView::widget([
            'dataProvider' => $alertSamplesProvider,
            'layout' => "{items}",
            'options' => [
                'tag' => 'div',
                'class' => 'list-wrapper-idle-samples',
                'id' => 'list-wrapper-idle-samples',
            ],
            'itemView' => '_alert_idle_samples',
            'emptyText' => false
        ]); ?>

        <?php if ($alertSamplesProvider->getTotalCount() > 5) { ?>
            <div class="alert-idle-samples d-flex" data-key="0" style="justify-content:center">
                <div class="batch-container">
                    Внимание, большая очередь из партий проб! Некоторые будут скрыты пока, очередь не освободится.
                </div>
            </div>
        <?php } ?>
    <?php } ?>

    <h1><?= $this->title ?></h1>

    <?php if (Yii::$app->user->can('service/create') && Yii::$app->user->identity->staff->job->departament->role == 'laboratory') { ?>
        <p>
            <?= Html::a('Создать пустое исследование', ['create-empty'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php } ?>
    <?php if (Yii::$app->user->can('service/seeLate')) { ?>
        <p class="buttons buttons-justify">
            <?= Html::a('Архив просроченных исследований', ['index-late'], ['class' => 'btn btn-primary button-justify-right']) ?>
        </p>
    <?php } ?>

    <?= $this->render('_index_grid', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider
    ]); ?>

</div>