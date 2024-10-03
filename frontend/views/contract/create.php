<?php

/** @var yii\web\View $this */
/** @var common\models\Contract $model */

$this->title = 'Создать договор';
$this->params['breadcrumbs'][] = ['label' => 'Организации', 'url' => ['organization/index']];
$this->params['breadcrumbs'][] = ['label' => $model->organization->getShortTitle(), 'url' => ['organization/view', 'id' => $model->organization_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contract-create">

    <h1><?= $this->title ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>