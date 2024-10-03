<?php

use common\models\Sample;
use common\models\Service;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\SampleSearch $sampleSearchModel */
/** @var yii\data\ActiveDataProvider $sampleDataProvider query from Sample model */
/** @var common\models\Service $model */

$this->title = $model->getShortTitle();
$this->params['breadcrumbs'][] = ['label' => 'Исследования', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="service-view">

    <h1><?= $this->title ?></h1>

    <?php if (Yii::$app->user->can('service/create') and (!$model->batch->payment or !$model->batch->payment->locked)) { ?>
        <p class="buttons buttons-justify">
            <?php if (!empty($model->sampleServices) and $model->getActiveSamples()->count() > 0) { ?>
                <?= Html::a($model->file ? 'Обновить результаты' : 'Загрузить результаты', ['upload-results', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php } elseif (empty($model->sampleServices)) { ?>
                <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите удалить этот элемент?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php } ?>
            <?php if (Yii::$app->user->can('admin') and !empty($model->completed_at) and $model->locked == 0) { ?>
                <?= Html::a('Подтвердить завершение', ['lock', 'id' => $model->id], ['class' => 'btn btn-primary button-justify-right']) ?>
            <?php } ?>
        </p>
    <?php } ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'batch_id',
                'format' => 'raw',
                'value' => function (Service $model) {
                    $batch = $model->batch;
                    return $batch->getLinkOnView(
                        Yii::$app->formatter->asDatetime($batch->employed_at, 'medium')
                    );
                }
            ],
            [
                'attribute' => 'organization',
                'format' => 'raw',
                'value' => function (Service $model) {
                    $contract = $model->batch->contract;
                    return $contract->organization->getLinkOnView(
                        $contract->getTitleActiveOrg(),
                        title: $contract->organization->getShortTitle(encode: false)
                    );
                },
                'visible' => Yii::$app->user->can('organization/see')
            ],
            [
                'attribute' => 'research',
                'format' => 'raw',
                'value' => function (Service $model) {
                    return Html::encode($model->research);
                }
            ],
            [
                'attribute' => 'price',
                'format' => ['decimal', 2],
                'filterInputOptions' => ['class' => 'form-control without-arrows', 'type' => 'number'],
                'value' => function (Service $model) {
                    return $model->price;
                },
                'visible' => Yii::$app->user->can('price_list/see')
            ],
            [
                'attribute' => 'pre_sum',
                'format' => ['decimal', 2],
                'filterInputOptions' => ['class' => 'form-control without-arrows', 'type' => 'number'],
                'value' => function (Service $model) {
                    return $model->pre_sum;
                },
                'visible' => Yii::$app->user->can('price_list/see')
            ],
            'started_at:raw',
            'predict_date:raw',
            [
                'attribute' => 'staff_id',
                'format' => 'raw',
                'value' => function (Service $model) {
                    $staff = $model->staff;
                    return $staff->getLinkOnView(
                        Html::encode($staff->fio),
                        title: $staff->fio
                    );
                }
            ],
            'completed_at:raw',
            [
                'label' => 'Файл с результатами',
                'format' => 'raw',
                'value' => function (Service $model) {
                    return $model->getIconFileResults();
                }
            ],
            'status:raw',
            [
                'label' => 'Акт оплаты',
                'format' => 'raw',
                'value' => function (Service $model) {
                    $payment = $model->batch->payment;
                    if ($payment) {
                        return $payment->getLinkOnView("Акт № {$payment->act_num}");
                    }
                },
            ],
        ],
    ]) ?>

    <div class="samples">
        <div class="accordion service-used-samples" id="accordion-service-samples">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        <?= $model->getAmountDetailed() ?>
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordion-service-samples">
                    <div class="accordion-body">
                        <?php Pjax::begin(); ?>

                        <?php if (Yii::$app->user->can('service/update') && $model->locked == 0) { ?>
                            <p>
                                <?= Html::a('Изменить состав проб', ['service/samples-edit', 'id' => $model->id], ['class' => 'btn btn-success', 'id' => 'create_object', 'data-pjax' => 0]) ?>
                            </p>
                        <?php } ?>

                        <?= GridView::widget([
                            'filterModel' => $sampleSearchModel,
                            'dataProvider' => $sampleDataProvider,
                            'columns' => [
                                [
                                    'class' => 'yii\grid\SerialColumn',
                                    'headerOptions' => ['class' => 'grid_column-serial']
                                ],
                                [
                                    'attribute' => 'identificator',
                                    'format' => 'raw',
                                    'value' => function (Sample $sample) use ($model) {
                                        $content = $sample->identificator;
                                        $another = null;
                                        if (!empty($sample->losted_at)) {
                                            $another = $sample->getStatusLost($model->id);
                                        }
                                        return $sample->getLinkOnView($content) . $another;
                                    }
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'urlCreator' => function ($action, Sample $model, $key, $index, $column) {
                                        return Url::toRoute([$action, 'id' => $model->id]);
                                    },
                                    'template' => '{delete}',
                                    'visible' => Yii::$app->user->can('sample/lost'),
                                    'buttons' => [
                                        'delete' => function ($url, Sample $model, $key) {
                                            return Html::a('<i class="fa-solid fa-trash-can"></i>', ['sample/delete', 'id' => $model->id], [
                                                'data-confirm' => Yii::t('yii', 'Вы уверены что хотите отметить пробу как "Потеряна"?'),
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
                                            ]);
                                        }
                                    ]
                                ],
                            ],
                            'summary' => 'Показано <strong>{begin}-{end}</strong> из <strong>{totalCount}</strong> проб',
                            'emptyText' => 'Проб не найдено'
                        ]); ?>

                        <?php Pjax::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>