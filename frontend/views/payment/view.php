<?php

use common\models\Payment;
use common\models\Service;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Payment $model */
/** @var yii\data\ActiveDataProvider $servicesDataProvider query from Service model */

$this->title = $model->getShortTitle();
$this->params['breadcrumbs'][] = ['label' => 'Акты оплаты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="payment-view">

    <h1><?= $this->title ?></h1>

    <p class="buttons buttons-justify">
        <?php if (!$model->locked) { ?>
            <?php if (Yii::$app->user->can('payment/update')) { ?>
                <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php } ?>
            <?php if (Yii::$app->user->can('payment/delete')) { ?>
                <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите удалить этот элемент?',
                        'method' => 'post',
                    ],
                ]) ?>
                <?php if ($model->checkSetAllValues()) { ?>
                    <?= Html::a('Закрепить акт', ['lock', 'id' => $model->id], [
                        'class' => 'btn btn-primary button-justify-right',
                        'data' => [
                            'confirm' => 'Вы уверены, что хотите закрепить акт? Он будет заблокирован для изменений.',
                            'method' => 'post',
                        ],
                    ]) ?>
                <?php } ?>
            <?php } ?>
        <?php } elseif (Yii::$app->user->can('admin')) { ?>
            <?= Html::a('Разблокировать', ['unlock', 'id' => $model->id], ['class' => 'btn btn-primary button-justify-right']) ?>
        <?php } ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'act_num',
            [
                'label' => 'Партия проб',
                'format' => 'raw',
                'value' => function (Payment $model) {
                    return $model->batch->getLinkOnView($model->batch->getShortTitle(true));
                }
            ],
            [
                'attribute' => 'organization_info',
                'format' => 'raw',
                'value' => function (Payment $model) {
                    $contract = $model->batch->contract;
                    return $contract->getLinkOnView(
                        $contract->getTitleActiveOrg(),
                        title: $contract->getTitleActiveOrg(true)
                    );
                },
                'visible' => Yii::$app->user->can('organization/see'),
            ],
            [
                'attribute' => 'fact_sum',
                'format' => ['decimal', 2],
                'value' => function (Payment $model) {
                    return $model->fact_sum;
                }
            ],
            'act_date:raw',
            'list_date:raw',
            'return_date:raw',
            'pay_date:raw',
            'status:raw',
            [
                'attribute' => 'file_act',
                'format' => 'raw',
                'value' => function (Payment $model) {
                    return $model->getIconFileAct();
                }
            ],
            [
                'attribute' => 'file_act_client',
                'format' => 'raw',
                'value' => function (Payment $model) {
                    return $model->getIconFileActClient();
                }
            ],
            [
                'attribute' => 'file_pay',
                'format' => 'raw',
                'value' => function (Payment $model) {
                    return $model->getIconFilePay();
                }
            ],
            [
                'attribute' => 'file_invoice',
                'format' => 'raw',
                'value' => function (Payment $model) {
                    return $model->getIconFileInvoice();
                }
            ],
        ],
    ]) ?>

    <?php if (Yii::$app->user->can('service/see')) { ?>
        <h2>Исследования:</h2>

        <div class="services">
            <?= GridView::widget([
                'dataProvider' => $servicesDataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['class' => 'grid_column-serial']
                    ],
                    [
                        'attribute' => 'research',
                        'format' => 'raw',
                        'value' => function (Service $service) {
                            return $service->getLinkOnView(
                                Html::encode($service->research),
                                title: $service->research
                            );
                        }
                    ],
                    [
                        'label' => 'Лаборатория',
                        'format' => 'raw',
                        'value' => function (Service $service) {
                            return $service->staff->job->departament->getShortTitle(true);
                        }
                    ],
                    [
                        'label' => 'Количество проб',
                        'format' => 'raw',
                        'value' => function (Service $service) {
                            return $service->getAmountDetailed();
                        }
                    ],
                    [
                        'attribute' => 'pre_sum',
                        'label' => 'Стоимость',
                        'format' => ['decimal', 2],
                    ]
                ],
                'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> исследований',
                'emptyText' => 'Исследований не найдено'
            ]); ?>
        </div>
    <?php } ?>

</div>