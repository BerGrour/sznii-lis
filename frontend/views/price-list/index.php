<?php

use common\models\PriceList;
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\CustomActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\PriceListSearch $searchModel */
/** @var array $tabTitles массив с наименованиями вкладок */
/** @var array $tabs массив с dataProvider's для вкладок */

$this->title = 'Каталог исследований';
$this->params['breadcrumbs'][] = $this->title;

$activeTab = true;
?>

<div class="price-list-index">

    <h1><?= $this->title ?></h1>

    <p class="buttons buttons-justify">
        <?php if (Yii::$app->user->can('price_list/create')) { ?>
            <?= Html::a('Создать новое исследование', ['create'], ['class' => 'btn btn-success']) ?>
        <?php } ?>

        <?php if (Yii::$app->user->can('archive_price_list/see')) { ?>
            <?= Html::a('Архив', ['archive-price-list/index'], ['class' => 'btn btn-primary button-justify-right']) ?>
        <?php } ?>
    </p>

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
                    aria-selected="true"
                ><b><?= Html::encode($tabTitles[$tabId]) ?></b></button>
            </li>
            <?php $activeTab = false ?>
        <?php endforeach ?>
    </ul>

    <div class="tab-content">
        <?php $activeTab = true ?>
        <?php foreach ($tabs as $tabId => $data) : ?>
            <div class="tab-pane <?= $activeTab ? 'active' : '' ?>" id="<?= $tabId ?>" role="tabpanel" aria-labelledby="<?= $tabId ?>-tab">
                <?php Pjax::begin(); ?>

                <?= GridView::widget([
                    'filterModel' => $searchModel,
                    'dataProvider' => $data,
                    'id' => "grid_{$tabId}",
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'headerOptions' => ['class' => 'grid_column-serial']
                        ],
                        [
                            'attribute' => 'research',
                            'format' => 'raw',
                            'value' => function (PriceList $model) {
                                return $model->getLinkOnView(
                                    Html::encode($model->research),
                                    title: $model->research
                                );
                            }
                        ],
                        [
                            'attribute' => 'price',
                            'format' => ['decimal', 2],
                            'filterInputOptions' => ['class' => 'form-control without-arrows', 'type' => 'number']
                        ],
                        [
                            'attribute' => 'status',
                            'visible' => Yii::$app->user->can('price_list/update'),
                            'value' => function (PriceList $model) {
                                return $model->getStatusName();
                            },
                            'filter' => [1 => PriceList::STATUS_ACTIVE, 0 => PriceList::STATUS_INACTIVE],
                            'filterInputOptions' => ['prompt' => '-', 'data-pjax' => 0, 'class' => 'form-control']
                        ],
                        [
                            'class' => CustomActionColumn::class,
                            'urlCreator' => function ($action, PriceList $model, $key, $index, $column) {
                                return Url::toRoute([$action, 'id' => $model->id]);
                            },
                            'template' => '{update}',
                            'visible' => Yii::$app->user->can('price_list/update'),
                        ],
                    ],
                    'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> исследований',
                    'emptyText' => 'Исследований в каталоге не найдено'
                ]); ?>

                <?php Pjax::end(); ?>
            </div>
            <?php $activeTab = false ?>
        <?php endforeach ?>
    </div>
</div>