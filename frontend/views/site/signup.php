<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\SignupForm $model */

use common\models\Organization;
use common\models\Staff;
use kartik\select2\Select2;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;

$this->title = 'Регистрация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= $this->title ?></h1>

    <p>Пожалуйста, заполните следующие поля для регистрации пользователя:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin([
                'id' => 'form-signup',
                'action' => Url::to(['/site/signup']),
                'options' => ['data-pjax' => true],
            ]); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'email') ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <?= $form->field($model, 'type')->radioList([
                    'staff' => 'Сотрудник',
                    'organization' => 'Организация'
                ], ['class' => 'compact-radio-group']); ?>

                <div class="form-group" id="staff-field" style="display: none;">
                    <?= $form->field($model, 'staff_id')->widget(Select2::class, [
                        'data' => ArrayHelper::map(Staff::find()->joinWith('user')->where(['user.staff_id' => null])->orderBy(['fio' => SORT_ASC])->all(), 'id', 'fio'),
                        'language' => 'ru',
                        'options' => [
                            'placeholder' => 'Выберите сотрудника...',
                            'id' => 'staff-field-select'
                        ],
                        'pluginOptions' => [
                            'ajax' => [
                                'url' => Url::to(['staff/list', 'empty' => true]),
                                'dataType' => 'json',
                                'delay' => 200,
                                'data' => new JsExpression('function(params) {
                                        return {term: params.term, page: params.page, limit: 20};
                                    }'),
                                'processResults' => new JsExpression('function(data) {
                                        return {results: data.results, pagination: { more: data.more }}
                                    }'),
                            ],
                        ]
                    ]) ?>
                </div>

                <div class="form-group" id="organization-field" style="display: none;">
                    <?= $form->field($model, 'organization_id')->widget(Select2::class, [
                        'data' => ArrayHelper::map(Organization::find()->joinWith('user')->where(['user.organization_id' => null])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                        'language' => 'ru',
                        'options' => ['placeholder' => 'Выберите организацию...'],
                        'pluginOptions' => [
                            'ajax' => [
                                'url' => Url::to(['organization/list', 'empty' => true]),
                                'dataType' => 'json',
                                'delay' => 200,
                                'data' => new JsExpression('function(params) {
                                        return {term: params.term, page: params.page, limit: 20};
                                    }'),
                                'processResults' => new JsExpression('function(data) {
                                        return {results: data.results, pagination: { more: data.more }}
                                    }'),
                            ],
                        ]
                    ]); ?>
                </div>

                <div class="form-group">
                    <?= Html::submitButton('Подтвердить', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>