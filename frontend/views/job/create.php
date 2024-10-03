<?php

/** @var yii\web\View $this */
/** @var common\models\Job $model */

$this->title = 'Создание должности';
$this->params['breadcrumbs'][] = ['label' => 'Отделы', 'url' => ['departament/index']];
$this->params['breadcrumbs'][] = ['label' => $model->departament->getShortTitle(), 'url' => ['departament/view', 'id' => $model->departament_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="job-create">

    <h1><?= $this->title ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>