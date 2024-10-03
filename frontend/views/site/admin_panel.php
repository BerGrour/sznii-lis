<?php

/** @var yii\web\View $this */

use common\models\Batch;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $samplesProvider Незарегестрированные пробы */
/** @var yii\data\ActiveDataProvider $unPaymentBatchesProvider Незарегистрированные акты */
/** @var yii\data\ActiveDataProvider $alertConfirmProvider Исследования с неполным количество проб */
/** @var yii\data\ActiveDataProvider $alertLateProvider Исследования с нарушенным сроком выполнения */


$this->title = 'Панель администратора';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-panel d-grid">

    <?= ListView::widget([
        'dataProvider' => $alertConfirmProvider,
        'layout' => "{items}",
        'options' => [
            'tag' => 'div',
            'class' => 'list-wrapper-confirm-service',
            'id' => 'list-wrapper-confirm-service',
        ],
        'itemView' => '_alert_confirm_service',
        'emptyText' => false
    ]); ?>

    <?= ListView::widget([
        'dataProvider' => $alertLateProvider,
        'layout' => "{items}",
        'options' => [
            'tag' => 'div',
            'class' => 'list-wrapper-late-service',
            'id' => 'list-wrapper-late-service',
        ],
        'itemView' => '_alert_late_service',
        'emptyText' => false
    ]); ?>

    <h1><?= $this->title ?></h1>

    <div class="buttons-panel">
        <ul class="list-group list-group-main">
            <li class="list-group-item">
                Создать новый объект:
            </li>
            <li class="list-group-item list-group-item-useless">
                <ul class="list-group list-group-horizontal">
                    <?= Html::a('Отдел', ['departament/create'], ['class' => 'list-group-item list-group-item-action btn btn-outline-primary']) ?>
                    <?= Html::a('Вид исследования', ['price-list/create'], ['class' => 'list-group-item list-group-item-action btn btn-outline-primary']) ?>
                    <?= Html::a('Сотрудник', ['staff/create-adaptive'], ['class' => 'list-group-item list-group-item-action btn btn-outline-primary']) ?>
                    <?= Html::a('Учетная запись', ['site/signup'], ['class' => 'list-group-item list-group-item-action btn btn-outline-primary']) ?>
                    <?= Html::a('Календарь', ['calendar/create'], ['class' => 'list-group-item list-group-item-action btn btn-outline-primary']); ?>
                </ul>
            </li>
        </ul>
    </div>

    <?php Pjax::begin(); ?>

    <h3>Незарегистрированные акты для партий проб</h3>
    <?= GridView::widget([
        'options' => [
            'id' => 'gridview_services'
        ],
        'dataProvider' => $unPaymentBatchesProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['class' => 'grid_column-serial']
            ],
            [
                'attribute' => 'employed_at',
                'format' => 'raw',
                'value' => function (Batch $model) {
                    return $model->getLinkOnView(Html::encode($model->getEmployedFormatDate()));
                }
            ],
            [
                'label' => 'Всего исследований',
                'format' => 'raw',
                'value' => function (Batch $model) {
                    return $model->getServices()->count();
                }
            ],
        ],
        'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> исследований',
        'emptyText' => 'Исследований проб не найдено'
    ]) ?>

    <h3>Незарегистрированные пробы</h3>
    <?= GridView::widget([
        'options' => [
            'id' => 'gridview_batches'
        ],
        'dataProvider' => $samplesProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['class' => 'grid_column-serial']
            ],
            [
                'attribute' => 'employed_at',
                'format' => 'raw',
                'value' => function (Batch $model) {
                    return $model->getLinkOnView($model->employed_at);
                }
            ],
            [
                'label' => 'Количество незанятых проб:',
                'format' => 'raw',
                'value' => function (Batch $model) {
                    return $model->getIdleSamplesCount(true);
                }
            ]
        ],
        'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> партий проб',
        'emptyText' => 'Партий проб не найдено'
    ]) ?>

    <?php Pjax::end(); ?>
</div>