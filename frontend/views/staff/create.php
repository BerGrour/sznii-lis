<?php

/** @var yii\web\View $this */
/** @var common\models\Staff $model */

$this->title = 'Создание сотрудника';
$this->params['breadcrumbs'][] = ['label' => 'Отделы', 'url' => ['departament/index']];
$this->params['breadcrumbs'][] = ['label' => $model->job->departament->getShortTitle(), 'url' => ['departament/view', 'id' => $model->job->departament_id]];
$this->params['breadcrumbs'][] = ['label' => $model->job->getShortTitle(), 'url' => ['job/view', 'id' => $model->job_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="staff-create">

    <h1><?= $this->title ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>