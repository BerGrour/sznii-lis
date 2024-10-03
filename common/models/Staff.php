<?php

namespace common\models;

use common\extensions\traits\BasicMethodsTrait;
use Yii;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "staff".
 *
 * @property int $id
 * @property string $fio
 * @property int $job_id
 * @property string $employ_date
 * @property string|null $leave_date
 * @property string|null $phone
 *
 * @property Job $job
 * @property Batch[] $batches
 * @property Service[] $services
 * @property User $user
 */
class Staff extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE_FROM_JOB = 'create'; // сценарий для формы с созданием в зависимости от должности
    const SCENARIO_CREATE_ADVANCED = 'create_advanced'; // сценарий для формы с адаптивным созданием
    const SCENARIO_TRANSFER = 'transfer'; // сценарий для формы с переводом сотрудника в другой отдел
    public $departament_select;
    public $job_select;

    use BasicMethodsTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'staff';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fio', 'job_id', 'employ_date'], 'required'],
            [['leave_date', 'phone'], 'default', 'value' => NULL],
            [['job_id'], 'integer'],
            [['phone'], 'safe'],
            [['employ_date', 'leave_date'], 'date', 'format' => 'php:Y-m-d'],
            [['phone'], 'match', 'pattern' => '/^\+7 \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}$/'],
            [['fio'], 'string', 'max' => 255],
            [['job_id'], 'exist', 'skipOnError' => true, 'targetClass' => Job::class, 'targetAttribute' => ['job_id' => 'id']],

            [['fio', 'job_id', 'employ_date', 'departament_select', 'job_select'], 'required', 'on' => self::SCENARIO_CREATE_ADVANCED],
            [['fio', 'job_id', 'employ_date'], 'required', 'on' => self::SCENARIO_CREATE_FROM_JOB],
            [['departament_select', 'job_select'], 'required', 'on' => self::SCENARIO_TRANSFER]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fio' => 'ФИО',
            'job_id' => 'Должность',
            'employ_date' => 'Дата зачисления',
            'leave_date' => 'Дата увольнения',
            'phone' => 'Телефон',

            'departament_select' => 'Отдел',
            'job_select' => 'Должность',
            'job_name' => 'Должность',
            'departament_name' => 'Отдел',
        ];
    }

    /**
     * Gets query for [[Job]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJob()
    {
        return $this->hasOne(Job::class, ['id' => 'job_id']);
    }

    /**
     * Gets query for [[Batches]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBatches()
    {
        return $this->hasMany(Batch::class, ['staff_id' => 'id']);
    }

    /**
     * Gets query for [[Services]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServices()
    {
        return $this->hasMany(Service::class, ['staff_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['staff_id' => 'id']);
    }

    /**
     * Получение заголовка
     * @param int $len ограничение длины
     * @return string
     */
    public function getShortTitle($len = 50)
    {
        $result = StringHelper::truncate(Html::encode($this->fio), $len);
        return $result;
    }
}
