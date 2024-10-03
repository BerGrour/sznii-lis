<?php

namespace common\components;

use common\models\Payment;
use common\models\Staff;
use Yii;
use yii\grid\ActionColumn;
use yii\helpers\Html;

class CustomActionColumn extends ActionColumn
{
    public function initDefaultButtons()
    {
        $this->buttons['delete'] = function ($url, $model, $key) {
            $title = Yii::t('yii', 'Удалить');
            $class = null;
            $additional_options = [
                'data-confirm' => Yii::t('yii', 'Вы уверены что хотите удалить этот элемент?'),
                'data-method' => 'post',
            ];
            if (($model instanceof Payment and $model->locked)
                or ($model instanceof Staff and $model->leave_date)
            ) {
                $class = 'disabled';
            }
            $options = array_merge([
                'class' => $class,
                'title' => $title,
                'aria-label' => $title,
                'data-pjax' => '0',
            ], $additional_options, $this->buttonOptions);

            $icon = '<i class="fa-solid fa-trash-can"></i>';
            return Html::a($icon, $url, $options);
        };
        $this->buttons['update'] = function ($url, $model, $key) {
            $title = Yii::t('yii', 'Изменить');
            $class = null;
            if (($model instanceof Payment) and $model->locked) {
                $class = 'disabled';
            }
            $options = array_merge([
                'class' => $class,
                'title' => $title,
                'aria-label' => $title,
                'data-pjax' => '0',
            ], $this->buttonOptions);

            $icon = '<i class="fa-solid fa-pencil"></i>';
            return Html::a($icon, $url, $options);
        };
    }
}
