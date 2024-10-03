<?php

use common\models\Sample;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Sample $model */

$this->title = $model->getShortTitle();
$this->params['breadcrumbs'][] = ['label' => 'Партии проб', 'url' => ['batch/index']];
$this->params['breadcrumbs'][] = ['label' => $model->batch->getShortTitle(), 'url' => ['batch/view', 'id' => $model->batch_id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sample-view">

    <h1><?= $this->title ?></h1>

    <p>
        <?php if (Yii::$app->user->can('sample/delete')) { ?>
            <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены, что хотите удалить этот элемент?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php } ?>
        <?php if (Yii::$app->user->can('service/create')) { ?>
            <?= Html::a(
                'Зарегистрировать исследования',
                ['service/once-select', 'id' => $model->id],
                ['class' => 'btn btn-success']) ?>
        <?php } ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'template' => function ($attribute, $index, $widget) {
            if ($attribute['value']) {
                return "<tr><th>{$attribute['label']}</th><td>{$attribute['value']}</td></tr>";
            }
        },
        'attributes' => [
            'identificator',
            [
                'attribute' => 'departament_id',
                'format' => 'raw',
                'value' => function (Sample $model) {
                    $departament = $model->departament;
                    return $departament->getLinkOnView(
                        Html::encode($departament->title),
                        title: $departament->title
                    );
                }
            ],
            [
                'attribute' => 'batch_id',
                'format' => 'raw',
                'value' => function (Sample $model) {
                    $batch = $model->batch;
                    return $batch->getLinkOnView(
                        Yii::$app->formatter->asDatetime($batch->employed_at, 'medium')
                    );
                }
            ],
            [
                'label' => 'Исследования',
                'format' => 'raw',
                'value' => function (Sample $model) {
                    return implode('; ', $model->getListServices($model->id, true));
                }
            ],
            'losted_at',
        ],
    ]) ?>

    <?php // потенциальный код для отображения исследований в которых учавствует проба. ?>

</div>