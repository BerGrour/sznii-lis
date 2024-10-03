<?php

use common\models\Contract;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Sample $model */

$this->title = 'Занесение пробы';
$this->params['breadcrumbs'][] = ['label' => 'Партии проб', 'url' => ['batch/index']];
$this->params['breadcrumbs'][] = ['label' => $model->batch->getShortTitle(), 'url' => ['batch/view', 'id' => $model->batch_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sample-create form-with-margins">
    <h1><?= $this->title ?></h1>

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'short-form'],
    ]); ?>

        <div class="form-group">
            <label class="control-label">Организация</label>

            <?= $form->field($model, 'contract_id')->widget(Select2::class, [
                'data' => ArrayHelper::map(
                    Contract::getActiveContracts()->all(),
                    'id',
                    'organization.name'
                ),
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Выберите организацию...',
                    'id' => 'organization-field-select'
                ],
            ])->label(false) ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Назад', Yii::$app->request->referrer, ['class' => 'btn btn-secondary']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>