<?php

namespace common\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "archive_price_list".
 *
 * @property int $id
 * @property int $research_id
 * @property float $price
 * @property string $updated_at
 *
 * @property PriceList $research
 */
class ArchivePriceList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'archive_price_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['research_id', 'price', 'updated_at'], 'required'],
            [['research_id'], 'integer'],
            [['price'], 'number'],
            [['updated_at'], 'safe'],
            [['research_id'], 'exist', 'skipOnError' => true, 'targetClass' => PriceList::class, 'targetAttribute' => ['research_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'research_id' => 'Исследование',
            'price' => 'Цена до, в руб.',
            'updated_at' => 'Дата изменения',
            'research_name' => 'Исследование',
            'departament_name' => 'Отдел',
        ];
    }

    /**
     * Gets query for [[Research]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResearch()
    {
        return $this->hasOne(PriceList::class, ['id' => 'research_id']);
    }

    /**
     * Получение заголовка
     * @param int $len ограничение длины
     * @return string
     */
    public function getShortTitle($len = 50)
    {
        $result = StringHelper::truncate(Html::encode($this->research->research), $len);
        return $result;
    }
}
