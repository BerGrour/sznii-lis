<?php

use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $staffsDataProvider query from Staff model */
/** @var yii\data\ActiveDataProvider $orgsDataProvider query from Organization model */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$tabs = [
    'staff' => $staffsDataProvider,
    'organization' => $orgsDataProvider,
];
$tabTitles = [
    'staff' => 'Сотрудники',
    'organization' => 'Организации',
];
$activeTab = true;
?>

<div class="user-index">
    <h1><?= $this->title ?></h1>

    <?php if (Yii::$app->user->can('user/create')) { ?>
        <p>
            <?= Html::a('Создать пользователя', ['/site/signup'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php } ?>

    <ul class="nav nav-tabs tabs-with-margins" id="myTab" role="tablist">
        <?php foreach ($tabs as $tabId => $tab) : ?>
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link <?= $activeTab ? 'active' : '' ?>"
                    id="<?= $tabId ?>-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#<?= $tabId ?>"
                    type="button"
                    role="tab"
                    aria-controls="<?= $tabId ?>"
                    aria-selected="true"><b><?= $tabTitles[$tabId] ?></b></button>
            </li>
            <?php $activeTab = false ?>
        <?php endforeach ?>
    </ul>

    <div class="tab-content">
        <?php $activeTab = true ?>
        <?php foreach ($tabs as $tabId => $data) : ?>
            <div class="tab-pane <?= $activeTab ? 'active' : '' ?>" id="<?= $tabId ?>" role="tabpanel" aria-labelledby="<?= $tabId ?>-tab">
                <?php Pjax::begin(); ?>

                    <?= $this->render("_{$tabId}_users", [
                        'filterModel' => $searchModel,
                        'dataProvider' => $data,
                    ]); ?>

                <?php Pjax::end(); ?>
            </div>
            <?php $activeTab = false ?>
        <?php endforeach ?>
    </div>
</div>