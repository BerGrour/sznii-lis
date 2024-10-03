<?php

/** @var yii\web\View $this */
/** @var common\models\Staff $model */

$this->title = 'Изменение сотрудника: ' . $model->getShortTitle();
$this->params['breadcrumbs'][] = ['label' => 'Отделы', 'url' => ['departament/index']];
$this->params['breadcrumbs'][] = ['label' => $model->job->departament->getShortTitle(), 'url' => ['departament/view', 'id' => $model->job->departament_id]];
$this->params['breadcrumbs'][] = ['label' => $model->job->getShortTitle(), 'url' => ['job/view', 'id' => $model->job_id]];
$this->params['breadcrumbs'][] = ['label' => $model->getShortTitle(), 'url' => ['staff/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="staff-update">

    <h1><?= $this->title ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>