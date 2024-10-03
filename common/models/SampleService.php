<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sample_service".
 *
 * @property int $id
 * @property int $sample_id
 * @property int $service_id
 *
 * @property Sample $sample
 * @property Service $service
 */
class SampleService extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sample_service';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sample_id', 'service_id'], 'required'],
            [['sample_id', 'service_id'], 'integer'],
            [['sample_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sample::class, 'targetAttribute' => ['sample_id' => 'id']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::class, 'targetAttribute' => ['service_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sample_id' => 'Проба',
            'service_id' => 'Исследование',
        ];
    }

    /**
     * Gets query for [[Sample]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSample()
    {
        return $this->hasOne(Sample::class, ['id' => 'sample_id']);
    }

    /**
     * Gets query for [[Service]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::class, ['id' => 'service_id']);
    }
}
