<?php

use common\models\Departament;
use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Staff $model */
/** @var yii\widgets\ActiveForm $form */

$this->title = "Перевод сотрудника: {$model->getShortTitle()}";
$this->params['breadcrumbs'][] = ['label' => 'Отделы', 'url' => ['departament/index']];
$this->params['breadcrumbs'][] = ['label' => $model->job->departament->getShortTitle(), 'url' => ['departament/view', 'id' => $model->job->departament_id]];
$this->params['breadcrumbs'][] = ['label' => $model->job->getShortTitle(), 'url' => ['job/view', 'id' => $model->job_id]];
$this->params['breadcrumbs'][] = ['label' => $model->getShortTitle(), 'url' => ['staff/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Перевод сотрудника';
?>
<div class="staff-create">

    <h1><?= $this->title ?></h1>

    <div class="service-form form-with-margins">

        <?php $form = ActiveForm::begin([
            'options' => ['class' => 'short-form'],
        ]); ?>

            <?= $form->field($model, 'departament_select')->dropDownList(
                ArrayHelper::map(Departament::find()->orderBy(['title' => SORT_ASC])->all(), 'id', 'title'),
                [
                    'id' => 'departament-field-drop',
                    'prompt' => 'Выберите отдел...'
                ],
            ); ?>

            <?= $form->field($model, 'job_select')->widget(DepDrop::class, [
                'options' => [
                    'id' => 'job-field-dep_drop',
                ],
                'pluginOptions' => [
                    'depends' => ['departament-field-drop'],
                    'url' => Url::to(['/job/departament-jobs']),
                    'placeholder' => 'Выберите должность...'
                ]
            ]); ?>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Назад', Yii::$app->request->referrer, ['class' => 'btn btn-secondary']) ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>