<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Job $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="job-form form-with-margins">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'short-form'],
    ]); ?>

        <?= $form->field($model, 'title')->textInput([
            'maxlength' => true,
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Назад', Yii::$app->request->referrer, ['class' => 'btn btn-secondary']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>