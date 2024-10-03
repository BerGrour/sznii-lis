<?php

use common\models\Staff;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Staff $model */

$this->title = $model->getShortTitle();
$this->params['breadcrumbs'][] = ['label' => 'Отделы', 'url' => ['departament/index']];
$this->params['breadcrumbs'][] = ['label' => $model->job->departament->getShortTitle(), 'url' => ['departament/view', 'id' => $model->job->departament_id]];
$this->params['breadcrumbs'][] = ['label' => $model->job->getShortTitle(), 'url' => ['job/view', 'id' => $model->job_id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="staff-view">

    <h1><?= $this->title ?></h1>

    <?php if (Yii::$app->user->can('staff/create')) { ?>
        <p>
            <?php if ($model->leave_date) { ?>
                <?= Html::a('Восстановить', ['restore', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
            <?php } ?>

            <?php if (!$model->leave_date) { ?>
                <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите удалить этот элемент?',
                        'method' => 'post',
                    ],
                ]) ?>
                <?= Html::a('Перевести', ['transfer', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php } ?>
        </p>
    <?php } ?>

    <?= DetailView::widget([
        'model' => $model,
        'template' => function ($attribute, $index, $widget) {
            if ($attribute['value']) {
                return "<tr><th>{$attribute['label']}</th><td>{$attribute['value']}</td></tr>";
            }
        },
        'attributes' => [
            [
                'attribute' => 'fio',
                'format' => 'raw',
                'value' => function (Staff $model) {
                    return Html::encode($model->fio);
                }
            ],
            [
                'label' => 'Учетная запись',
                'visible' => Yii::$app->user->can('user/see'),
                'format' => 'raw',
                'value' => function (Staff $model) {
                    $user = $model->user;
                    if ($user) {
                        return $user->getLinkOnView($user->username);
                    }
                    $content = '<strong style="color:red;">Не привязано</strong>';
                    if (Yii::$app->user->can('user/create')) {
                        $content = Html::a(
                            'Создать пользователя',
                            ['/site/signup'],
                            ['class' => 'btn btn-success btn-sm', 'target' => '_blank']
                        );
                    }
                    return $content;
                }
            ],
            [
                'label' => 'Отдел',
                'format' => 'raw',
                'value' => function (Staff $model) {
                    $departament = $model->job->departament;
                    return $departament->getLinkOnView(
                        Html::encode($departament->title),
                        title: $departament->title
                    );
                }
            ],
            [
                'attribute' => 'job_id',
                'format' => 'raw',
                'value' => function (Staff $model) {
                    $job = $model->job;
                    return $job->getLinkOnView(
                        Html::encode($job->title),
                        title: $job->title
                    );
                }
            ],
            'employ_date',
            'leave_date',
            'phone',
        ],
    ]); ?>

    <?php // TODO: выводить либо пробы, либо исследования, в зависимости где работает сотрудник ?>

</div>