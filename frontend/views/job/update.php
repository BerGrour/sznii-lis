<?php

/** @var yii\web\View $this */
/** @var common\models\Job $model */

$this->title = 'Изменение должности: ' . $model->getShortTitle();

$this->params['breadcrumbs'][] = ['label' => 'Отделы', 'url' => ['departament/index']];
$this->params['breadcrumbs'][] = ['label' => $model->departament->getShortTitle(), 'url' => ['departament/view', 'id' => $model->departament_id]];
$this->params['breadcrumbs'][] = ['label' => $model->getShortTitle(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="job-update">

    <h1><?= $this->title ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>