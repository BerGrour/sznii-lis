<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\ServiceSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider query from Service model */

$this->title = 'Архив просроченных исследований';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-index">

    <h1><?= $this->title ?></h1>

    <p class="buttons buttons-justify">
        <?= Html::a('Все исследования', ['index'], ['class' => 'btn btn-primary button-justify-right']) ?>
    </p>

    <?= $this->render('_index_grid', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider
    ]); ?>

</div>