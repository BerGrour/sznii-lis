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
/** @var yii\widgets\ActiveForm $form */

$this->title = 'Изменение партии проб: ' . $model->getShortTitle();
$this->params['breadcrumbs'][] = ['label' => 'Партии проб', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getShortTitle(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменение';
?>
<div class="batch-update">

    <h1><?= $this->title ?></h1>

    <div class="batch-form form-with-margins">

        <?php $form = ActiveForm::begin([
            'id' => 'form-batch',
            'action' => Url::to(['/batch/update', 'id' => $model->id]),
            'options' => ['class' => 'short-form', 'data-pjax' => true],
        ]); ?>

            <div class="form-group">
                <label class="control-label">Организация</label>
                <?= $form->field($model, 'contract_id')->widget(Select2::class, [
                    'data' => ArrayHelper::map(Contract::getActiveContracts()->joinWith('organization')->orderBy(['organization.name' => SORT_ASC, 'number' => SORT_DESC])->all(), 'id', 'TitleActiveOrg'),
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

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Назад', Yii::$app->request->referrer, ['class' => 'btn btn-secondary']) ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>


</div>