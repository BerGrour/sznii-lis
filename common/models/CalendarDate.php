<?php

namespace common\models;

use Yii;

/**
 * @property int $id
 * @property int $year_id
 * @property string $date
 * 
 * @property CalendarYear $year
 */
class CalendarDate extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'calendar_date';
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            [['year_id', 'date'], 'required'],
            [['year_id'], 'integer'],
            [['date'], 'safe'],
            [['date'], 'match', 'pattern' => '/^(0[1-9]|[12]\d|3[01])\.(0[1-9]|1[0-2])$/'],
            [['year_id'], 'exist', 'skipOnError' => true, 'targetClass' => CalendarYear::class, 'targetAttribute' => ['year_id' => 'id']],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'year_id' => 'Год',
            'date' => 'Дата'
        ];
    }

    /**
     * Gets query for [[CalendarYear]].
     * 
     * @return Yii\db\ActiveQuery
     */
    public function getYear()
    {
        return $this->hasOne(CalendarYear::class, ['id' => 'year_id']);
    }
}
