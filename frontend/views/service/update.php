<?php

use kartik\file\FileInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Service $model */
/** @var yii\widgets\ActiveForm $form */

$this->title = 'Загрузка результатов для: ' . $model->getShortTitle();
$this->params['breadcrumbs'][] = ['label' => 'Исследования', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getShortTitle(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Загрузка результатов';

$laboratory_id = $model->staff->job->departament_id;
?>
<div class="service-update">

    <h1><?= $this->title ?></h1>

    <div class="service-form form-with-margins">

        <?php $form = ActiveForm::begin([
            'options' => ['class' => 'short-form'],
        ]); ?>

            <?php if (empty($model->file)) { ?>
                <?= $form->field($model, 'uploadedFile')->widget(FileInput::class, [
                    'language' => 'ru',
                    'pluginOptions' => [
                        'showPreview' => false,
                        'showCaption' => true,
                        'showRemove' => false,
                        'showUpload' => false,
                        'initialPreview' => [
                            $model->getFilePreview()
                        ],
                        'maxFileSize' => 2800, // Кб
                        'overwriteInitial' => true
                    ]
                ]); ?>
            <?php } else { ?>
                <div class="form-group">
                    <label class="control-label">Файл с результатами:</label>
                    <div class="input-with-button d-flex">
                        <?= $form->field($model, 'uploadedFile')->textInput([
                            'value' => $model->getFileName(),
                            'class' => 'form-control input-with-link',
                            'type' => 'button',
                            'title' => 'Открыть',
                            'onclick' => 'window.open("' . $model->getFilePreview() . '", "_blank");'
                        ])->label(false) ?>
                        <?= Html::a(
                            'Удалить файл',
                            Url::to(['/service/delete-file', 'id' => $model->id]),
                            [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => 'Вы уверены, что хотите удалить этот файл?',
                                    'method' => 'post',
                                ],
                            ]
                        ); ?>
                    </div>
                </div>
            <?php } ?>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Назад', Yii::$app->request->referrer, ['class' => 'btn btn-secondary']) ?>
            </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>