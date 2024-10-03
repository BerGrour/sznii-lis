<?php

namespace common\extensions\traits;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Трэйт для базовых методов моделей:
 * Contract, Departament, Job, Organization, Payment, PriceList, Sample, Service, Staff.
 *
 */
trait BasicMethodsTrait
{
    /**
     * Ссылка на детальную страницу
     * @return string URL
     */
    public function getUrl()
    {
        $model_name = preg_replace('/\_/', '-', $this->tableName());

        return Url::to([$model_name . '/view', 'id' => $this->id]);
    }

    /**
     * Метод возвращающий кликабельный заголовок, направляющий на детальную страницу
     * 
     * @param string $content наименование для ссылки
     * @param string $target атрибут target тега <a>, дефолт = "_self"
     * @param string $title атрибут title html-тега
     * @return string hyperlink
     */
    public function getLinkOnView($content, $target = '_self', $title = null)
    {
        if (!$title) $title = $content;
        if (Yii::$app->user->can(self::tableName() . '/see')) {
            $result = Html::a(
                $content,
                $this->getUrl(),
                [
                    'class' => 'text-link',
                    'target' => $target,
                    'title' => $title,
                    'data-pjax' => '0'
                ]
            );
        } else {
            $result = $content;
        }

        return $result;
    }

    /**
     * Получение отформатированной даты (10 июн. 2024 г., 09:14:59) 
     * @return string
     */
    public function getFormatDateMedium($date)
    {
        $result = Yii::$app->formatter->asDatetime(
            $date,
            'medium'
        );
        return $result;
    }
}
