<?php

/** @var yii\web\View $this */
/** @var common\models\Contract $model */

$this->title = 'Изменение ' . $model->getShortTitle();
$this->params['breadcrumbs'][] = ['label' => 'Организации', 'url' => ['organization/index']];
$this->params['breadcrumbs'][] = ['label' => $model->organization->getShortTitle(), 'url' => ['organization/view', 'id' => $model->organization_id]];
$this->params['breadcrumbs'][] = ['label' => $model->getShortTitle(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="contract-update">

    <h1><?= $this->title ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>