<?php 

use common\models\PriceList;
use common\models\Sample;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var array $ids */
/** @var common\models\Batch $batch */

$this->title = 'Создание нескольких исследований';
$this->params['breadcrumbs'][] = ['label' => 'Партии проб', 'url' => ['batch/index']];
$this->params['breadcrumbs'][] = ['label' => $batch->getShortTitle(), 'url' => ['batch/view', 'id' => $batch->id]];
$this->params['breadcrumbs'][] = $this->title;

$samples = Sample::find()->where(['in', 'id', $ids])->all();
?>

<div class="service-create-bulk">

    <h1><?= $this->title ?></h1>

    <p>
        Для проб: 
        <?php foreach ($samples as $sample) {
            echo $sample->identificator . "; "; 
        } ?>
    </p>

    <div class="service-form form-with-margins">

        <?php $form = ActiveForm::begin([
            'options' => ['class' => 'short-form'],
            
        ]); ?>

            <?= $form->field($batch, 'researches')->widget(Select2::class,[
                'data' => ArrayHelper::map(PriceList::getListLaboratoryResearches(Yii::$app->user->identity->staff->job->departament_id), 'id', 'research'),
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Выберите виды исследований...',
                    'id' => 'research-field-select-multiple',
                    'multiple' => true
                ]
            ])->label('Виды исследований') ?>

            <div class="form-group">
                <?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Назад', Yii::$app->request->referrer, ['class' => 'btn btn-secondary']) ?>
            </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>