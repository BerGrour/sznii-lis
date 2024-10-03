<?php

namespace common\models;

use common\extensions\traits\BasicMethodsTrait;
use Yii;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "batch".
 *
 * @property int $id
 * @property string $employed_at
 * @property int $staff_id
 * @property int $contract_id
 * @property int $payment_id
 *
 * @property Contract $contract
 * @property Sample[] $samples
 * @property Service[] $services
 * @property Payment $payment
 * @property Staff $staff
 */
class Batch extends \yii\db\ActiveRecord
{
    use BasicMethodsTrait;
    public $researches = [];
    public $labs_amount = [];    // массив с распределением количества проб из партии по каждой лаборатории

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'batch';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employed_at', 'staff_id'], 'required'],
            [['contract_id'], 'required', 'message' => 'Требуется выбрать организацию'],
            [['employed_at'], 'safe'],
            [['staff_id', 'contract_id', 'payment_id'], 'integer'],
            [['contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contract::class, 'targetAttribute' => ['contract_id' => 'id']],
            [['staff_id'], 'exist', 'skipOnError' => true, 'targetClass' => Staff::class, 'targetAttribute' => ['staff_id' => 'id']],
            [['payment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payment::class, 'targetAttribute' => ['payment_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'employed_at' => 'Дата поступления',
            'staff_id' => 'Сотрудник',
            'contract_id' => 'Договор',
            'payment_id' => 'Акт оплаты',

            'organization_name' => 'Организация',
            'amount' => 'Количество',
        ];
    }

    /**
     * Gets query for [[Contract]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContract()
    {
        return $this->hasOne(Contract::class, ['id' => 'contract_id']);
    }

    /**
     * Gets query for [[Samples]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSamples()
    {
        return $this->hasMany(Sample::class, ['batch_id' => 'id']);
    }

    /**
     * Gets query for [[Services]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServices()
    {
        return $this->hasMany(Service::class, ['batch_id' => 'id']);
    }

    /**
     * Gets query for [[Payment]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayment()
    {
        return $this->hasOne(Payment::class, ['id' => 'payment_id']);
    }

    /**
     * Gets query for [[Staff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStaff()
    {
        return $this->hasOne(Staff::class, ['id' => 'staff_id']);
    }
    /**
     * Получение отформатированной даты принятия партии проб
     * @return string
     */
    public function getEmployedFormatDate()
    {
        return $this->getFormatDateMedium($this->employed_at);
    }

    /**
     * Получение заголовка
     * @param int $len ограничение длины
     * @return string
     */
    public function getShortTitle($len = 50)
    {
        $result = StringHelper::truncate(
            'Партия проб: ' . $this->getEmployedFormatDate(),
            $len
        );
        return $result;
    }

    /**
     * Массовое создание проб для каждой лаборатории.
     * 
     * @param array $array массив, где key - departament_id, value - количество проб
     * @param int $batch_id индекс партии
     * 
     * @return bool
     */
    public static function bulkCreateSamples($array, $batch)
    {
        $success = true;
        foreach ($array as $key => $value) {
            if ((int)$value > 0) {
                $lab = Departament::findOne((int)$key);
                for ($i = 1; $i <= (int)$value; $i++) {
                    $sample = new Sample();
                    $sample->batch_id = $batch->id;
                    $sample->departament_id = $lab->id;
                    $sample->num = $sample->createNumber(
                        $batch->employed_at,
                        $lab->id,
                        $lab->period
                    );
                    $sample->identificator = $sample->createIdentificator(
                        $batch->employed_at,
                        $lab->abbreviation,
                        $sample->num
                    );
                    if (!$sample->save()) $success = false;
                }
            }
        }
        return $success;
    }

    /**
     * Выводит количество незанятых проб для партии по лабораториям
     * 
     * @param bool $without_selection без разделения по лабораториям, default = false
     * @return int|string|null Количество незанятых проб
     */
    public function getIdleSamplesCount($without_selection = false)
    {
        $sample = Sample::find()
            ->where(['busy' => 0, 'losted_at' => null, 'batch_id' => $this->id]);

        if ($without_selection) {
            return $sample->count();
        }
        return $sample->andWhere(['departament_id' => Yii::$app->user->identity->staff->job->departament_id])->count();
    }

    /**
     * Получение незавершенного исследования из партии
     * @return array|Yii\db\ActiveRecord|null
     */
    public function getServiceInProcess()
    {
        return Service::find()
            ->where(['batch_id' => $this->id, 'locked' => 0])
            ->one();
    }
}
