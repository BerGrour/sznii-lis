<?php

use kartik\file\FileInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Payment $model */

$organization = $model->batch->contract->organization;

$this->title = 'Загрузка подписанного акта';
$this->params['breadcrumbs'][] = ['label' => $organization->getShortTitle(), 'url' => ['client/index', 'org_id' => $organization->id]];
$this->params['breadcrumbs'][] = $model->getShortTitle();
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-upload-act-client">

    <h1><?= $this->title ?></h1>

    <div class="payment-form form-with-margins">

        <?php $form = ActiveForm::begin([
            'options' => ['class' => 'short-form'],
        ]); ?>

            <?php if (empty($model->fileActClient)) { ?>
                <?= $form->field($model, 'uploadedFileActClient')->widget(FileInput::class, [
                    'language' => 'ru',
                    'pluginOptions' => [
                        'showPreview' => false,
                        'showCaption' => true,
                        'showRemove' => false,
                        'showUpload' => false,
                        'initialPreview' => [
                            $model->getFilePreview('fileActClient')
                        ],
                        'maxFileSize' => 2800, // Кб
                        'overwriteInitial' => true
                    ]
                ]); ?>
            <?php } else { ?>
                <div class="form-group">
                    <label class="control-label">Файл подписанного акта:</label>
                    <div class="input-with-button d-flex">
                        <?= $form->field($model, 'uploadedfileActClient')->textInput([
                            'value' => $model->getFileName("fileActClient"),
                            'class' => 'form-control input-with-link',
                            'type' => 'button',
                            'title' => 'Открыть',
                            'onclick' => 'window.open("' . $model->getFilePreview("fileActClient") . '", "_blank");'
                        ])->label(false) ?>
                        <?= Html::a(
                            'Удалить файл',
                            Url::to(['/payment/delete-client-file', 'id' => $model->id]),
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