<?php

use kartik\date\DatePicker;
use kartik\file\FileInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Payment $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="payment-form form-with-margins">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'short-form'],
    ]); ?>

        <?= $form->field($model, 'act_num')->textInput(['type' => 'number', 'min' => 0]) ?>

        <?= $form->field($model, 'act_date')->widget(DatePicker::class, [
            'name' => 'datepicker-payment-act_date',
            'language' => 'ru',
            'type' => DatePicker::TYPE_INPUT,
            'pluginOptions' => [
                'todayHighlight' => true,
                'autoclose' => true,
                'orientation' => 'bottom',
                'format' => 'yyyy-mm-dd',
            ],
        ]) ?>

        <?= $form->field($model, 'list_date')->widget(DatePicker::class, [
            'name' => 'datepicker-payment-list_date',
            'language' => 'ru',
            'type' => DatePicker::TYPE_INPUT,
            'pluginOptions' => [
                'todayHighlight' => true,
                'autoclose' => true,
                'orientation' => 'bottom',
                'format' => 'yyyy-mm-dd',
            ]
        ]) ?>

        <?= $form->field($model, 'return_date')->widget(DatePicker::class, [
            'name' => 'datepicker-payment-return_date',
            'language' => 'ru',
            'type' => DatePicker::TYPE_INPUT,
            'pluginOptions' => [
                'todayHighlight' => true,
                'autoclose' => true,
                'orientation' => 'bottom',
                'format' => 'yyyy-mm-dd',
            ]
        ]) ?>

        <?= $form->field($model, 'pay_date')->widget(DatePicker::class, [
            'name' => 'datepicker-payment-pay_date',
            'language' => 'ru',
            'type' => DatePicker::TYPE_INPUT,
            'pluginOptions' => [
                'todayHighlight' => true,
                'autoclose' => true,
                'orientation' => 'bottom',
                'format' => 'yyyy-mm-dd',
            ]
        ]) ?>

        <?php if (empty($model->fileAct)) { ?>
            <?= $form->field($model, 'uploadedFileAct')->widget(FileInput::class, [
                'language' => 'ru',
                'pluginOptions' => [
                    'showPreview' => false,
                    'showCaption' => true,
                    'showRemove' => false,
                    'showUpload' => false,
                    'initialPreview' => [
                        $model->getFilePreview("fileAct")
                    ],
                    'maxFileSize' => 2800, // Кб
                    'overwriteInitial' => true
                ]
            ]); ?>
        <?php } else { ?>
            <div class="form-group">
                <label class="control-label">Файл акта:</label>
                <div class="input-with-button d-flex">
                    <?= $form->field($model, 'uploadedFileAct')->textInput([
                        'value' => $model->getFileName("fileAct"),
                        'class' => 'form-control input-with-link',
                        'type' => 'button',
                        'title' => 'Открыть',
                        'onclick' => 'window.open("' . $model->getFilePreview("fileAct") . '", "_blank");'
                    ])->label(false) ?>
                    <?= Html::a(
                        'Удалить файл',
                        Url::to(['/payment/delete-file', 'id' => $model->id, 'file' => 'fileAct']),
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

        <?php if (empty($model->filePay)) { ?>
            <?= $form->field($model, 'uploadedFilePay')->widget(FileInput::class, [
                'language' => 'ru',
                'pluginOptions' => [
                    'showPreview' => false,
                    'showCaption' => true,
                    'showRemove' => false,
                    'showUpload' => false,
                    'initialPreview' => [
                        $model->getFilePreview("filePay")
                    ],
                    'maxFileSize' => 2800, // Кб
                    'overwriteInitial' => true
                ]
            ]); ?>
        <?php } else { ?>
            <div class="form-group">
                <label class="control-label">Файл счёт:</label>
                <div class="input-with-button d-flex">
                    <?= $form->field($model, 'uploadedFilePay')->textInput([
                        'value' => $model->getFileName("filePay"),
                        'class' => 'form-control input-with-link',
                        'type' => 'button',
                        'title' => 'Открыть',
                        'onclick' => 'window.open("' . $model->getFilePreview("filePay") . '", "_blank");'
                    ])->label(false) ?>
                    <?= Html::a(
                        'Удалить файл',
                        Url::to(['/payment/delete-file', 'id' => $model->id, 'file' => 'filePay']),
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

        <?php if (empty($model->fileInvoice)) { ?>
            <?= $form->field($model, 'uploadedFileInvoice')->widget(FileInput::class, [
                'language' => 'ru',
                'pluginOptions' => [
                    'showPreview' => false,
                    'showCaption' => true,
                    'showRemove' => false,
                    'showUpload' => false,
                    'initialPreview' => [
                        $model->getFilePreview("fileInvoice")
                    ],
                    'maxFileSize' => 2800, // Кб
                    'overwriteInitial' => true
                ]
            ]); ?>
        <?php } else { ?>
            <div class="form-group">
                <label class="control-label">Файл счёт-фактуры:</label>
                <div class="input-with-button d-flex">
                    <?= $form->field($model, 'uploadedFileInvoice')->textInput([
                        'value' => $model->getFileName("fileInvoice"),
                        'class' => 'form-control input-with-link',
                        'type' => 'button',
                        'title' => 'Открыть',
                        'onclick' => 'window.open("' . $model->getFilePreview("fileInvoice") . '", "_blank");'
                    ])->label(false) ?>
                    <?= Html::a(
                        'Удалить файл',
                        Url::to(['/payment/delete-file', 'id' => $model->id, 'file' => 'fileInvoice']),
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