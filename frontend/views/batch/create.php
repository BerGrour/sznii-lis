<?php

use common\models\Contract;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Batch $model */
/** @var array $departaments список лабораторий */
/** @var yii\widgets\ActiveForm $form */

$this->title = 'Прием партии проб';
$this->params['breadcrumbs'][] = ['label' => 'Партии', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="batch-create">

    <h1><?= $this->title ?></h1>

    <div class="batch-form form-with-margins">

        <?php $form = ActiveForm::begin([
            'id' => 'form-batch',
            'action' => Url::to(['/batch/create']),
            'options' => ['class' => 'short-form', 'data-pjax' => true],
        ]); ?>

            <div class="form-group">
                <label class="control-label">Организация</label>
                <?= $form->field($model, 'contract_id')->widget(Select2::class, [
                    'data' => ArrayHelper::map(Contract::getActiveContracts()->joinWith('organization')->orderBy(['organization.name' => SORT_ASC, 'number' => SORT_DESC])->all(), 'id', 'titleActiveOrg'),
                    'language' => 'ru',
                    'options' => [
                        'placeholder' => 'Выберите организацию...',
                        'id' => 'organization-field-select'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'formatSelection' => new JsExpression('function (data) {
                            return data.replace(/&lt;/g, "<").replace(/&gt;/g, ">");
                        }'),
                    ]
                ])->label(false) ?>
            </div>

            <label class="control-label laboratories-amount-label">Введите количество проб для лабораторий:</label>
            <div class="form-group form-group-horizontaly laboratories-amount-form">
                <?php foreach ($departaments as $number => $departament) { ?>
                    <?php if ($number == 0) { ?>
                        <div class="first-bordered-form-group">
                    <?php } else { ?>
                        <div class="bordered-form-group">
                    <?php } ?>
                    <?= $form->field($model, 'labs_amount[' . $departament->id . ']')
                        ->label(Html::encode($departament->title))->textInput(['type' => 'number', 'min' => 0, 'max' => 9999]); ?>
                    </div>

                <?php } ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Назад', Yii::$app->request->referrer, ['class' => 'btn btn-secondary']) ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>