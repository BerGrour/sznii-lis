<?php

use common\models\Contract;
use common\models\Payment;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\components\CustomActionColumn;
use yii\grid\GridView;
use yii\web\JsExpression;
use yii\widgets\ListView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider query from Payment model */
/** @var common\models\PaymentSearch $searchModel */
/** @var yii\data\ActiveDataProvider $alertBatchProvider query from Batch model */

$this->title = 'Акты оплаты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-index">

    <?php if (Yii::$app->user->can('payment/create')) { ?>
        <?= ListView::widget([
            'dataProvider' => $alertBatchProvider,
            'layout' => "{items}",
            'options' => [
                'tag' => 'div',
                'class' => 'list-wrapper-completed-batch',
                'id' => 'list-wrapper-completed-batch',
            ],
            'itemView' => '_alert_completed_batches',
            'emptyText' => false
        ]); ?>

        <?php if ($alertBatchProvider->getTotalCount() > 5) { ?>
            <div class="alert-completed-samples d-flex" data-key="0" style="justify-content:center">
                <div class="batch-container">
                    Внимание, большая очередь из партий проб! Некоторые будут скрыты пока, очередь не освободится.
                </div>
            </div>
        <?php } ?>
    <?php } ?>

    <h1><?= $this->title ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['class' => 'grid_column-serial']
            ],
            [
                'attribute' => 'act_num',
                'format' => 'raw',
                'filterInputOptions' => ['class' => 'form-control without-arrows', 'type' => 'number'],
                'value' => function (Payment $model) {
                    return $model->getLinkOnView("Акт № {$model->act_num}");
                }
            ],
            [
                'attribute' => 'organization_info',
                'format' => 'raw',
                'value' => function (Payment $model) {
                    $contract = $model->batch->contract;
                    return $contract->getTitleActiveOrg();
                },
                'visible' => Yii::$app->user->can('organization/see'),
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'organization_info',
                    'language' => 'ru',
                    'data' => ArrayHelper::map(Contract::find()->joinWith('organization')->orderBy(['organization.name' => SORT_ASC, 'number' => SORT_DESC])->all(), 'id', 'titleActiveOrg'),
                    'value' => 'organization.name',
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'Выберите организацию'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'formatSelection' => new JsExpression('function (data) {
                            return data.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
                        }')
                    ]
                ])
            ],
            [
                'attribute' => 'fact_sum',
                'format' => ['decimal', 2],
                // 'format' => 'raw',
                'filterInputOptions' => ['class' => 'form-control without-arrows', 'type' => 'number'],
            ],
            [
                'attribute' => 'act_date',
                'headerOptions' => ['class' => 'grid_column-date_range'],
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'convertFormat' => true,
                    'presetDropdown' => true,
                    'attribute' => 'act_date',
                    'options' => ['placeholder' => 'Выберите диапазон'],
                    'language' => 'ru',
                    'pluginOptions' => [
                        'todayHighlight' => true,
                        'autoclose' => true,
                        'locale' => [
                            'format' => 'Y-m-d',
                            'separator' => ' | ',
                        ],
                    ]
                ])
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function (Payment $model) {
                    return $model->getStatus();
                },
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'status',
                    'language' => 'ru',
                    'data' => [0 => 'Не завершено', 1 => 'Завершено'],
                    'value' => 'status',
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'Выберите состояние'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'formatSelection' => new JsExpression('function (data) {
                            return data.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
                        }'),
                    ]
                ])
            ],
            [
                'label' => 'Файлы',
                'format' => 'raw',
                'value' => function (Payment $model) {
                    return $model->getPaymentFiles();
                },
                'headerOptions' => ['class' => 'grid_column-files']
            ],
            [
                'class' => CustomActionColumn::class,
                'urlCreator' => function ($action, Payment $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'template' => '{update}{delete}',
                'visible' => Yii::$app->user->can('payment/delete'),
            ],
        ],
    ]); ?>

</div>