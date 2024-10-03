<?php

namespace common\models;

use common\extensions\traits\BasicMethodsTrait;
use Yii;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "job".
 *
 * @property int $id
 * @property string $title
 * @property int $departament_id
 *
 * @property Departament $departament
 * @property Staff[] $staff
 */
class Job extends \yii\db\ActiveRecord
{
    use BasicMethodsTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'job';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'departament_id'], 'required'],
            [['departament_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['departament_id'], 'exist', 'skipOnError' => true, 'targetClass' => Departament::class, 'targetAttribute' => ['departament_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'departament_id' => 'Отдел',
        ];
    }

    /**
     * Gets query for [[Departament]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepartament()
    {
        return $this->hasOne(Departament::class, ['id' => 'departament_id']);
    }

    /**
     * Gets query for [[Staff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStaff()
    {
        return $this->hasMany(Staff::class, ['job_id' => 'id']);
    }

    /**
     * Получение заголовка
     * @param int $len ограничение длины
     * @return string
     */
    public function getShortTitle($len = 50)
    {
        $result = StringHelper::truncate(Html::encode($this->title), $len);
        return $result;
    }

    /**
     * Метод для получения списка должностей соответствующего отдела 
     * 
     * @param int $departament_id индекс отдела
     * @return array
     */
    static function getDepartamentJobsList($departament_id)
    {
        $jobs = Job::find()->where(['departament_id' => $departament_id])
            ->orderBy(['title' => SORT_ASC])->select('id, title as name')->asArray()->all();
        return $jobs;
    }
}
