<?php

namespace common\models;

use common\extensions\traits\BasicMethodsTrait;
use Yii;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "contract".
 *
 * @property int $id
 * @property int $number
 * @property int $organization_id
 * @property string $start_date
 * @property string $end_date
 * @property string|null $list_date
 *
 * @property Organization $organization
 * @property Batch[] $batches
 */
class Contract extends \yii\db\ActiveRecord
{
    use BasicMethodsTrait;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contract';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number', 'organization_id', 'start_date', 'end_date'], 'required'],
            [['list_date'], 'default', 'value' => NULL],
            [['number', 'organization_id'], 'integer'],
            [['start_date', 'end_date', 'list_date'], 'date', 'format' => 'php:Y-m-d'],
            [['organization_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::class, 'targetAttribute' => ['organization_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Номер',
            'organization_id' => 'Организация',
            'start_date' => 'Дата начала',
            'end_date' => 'Дата окончания',
            'list_date' => 'Дата поступления оригиналов',
        ];
    }

    /**
     * Gets query for [[Organization]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::class, ['id' => 'organization_id']);
    }

    /**
     * Gets query for [[Batches]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBatches()
    {
        return $this->hasMany(Batch::class, ['contract_id' => 'id']);
    }

    /**
     * Получение заголовка
     * @param int $len ограничение длины
     * @return string
     */
    public function getShortTitle($len = 50)
    {
        $result = StringHelper::truncate('Договор № ' . $this->number, $len);
        return $result;
    }

    /**
     * Возвращает ActiveQuery активных договоров.
     */
    public static function getActiveContracts()
    {
        $query = self::find()
            ->andWhere(['<=', 'contract.start_date', date('Y-m-d')])
            ->andWhere(['>=', 'contract.end_date', date('Y-m-d')]);

        return $query;
    }

    /**
     * Возвращает наименование организации и номер соответствующего договора
     * @param bool $encode убрать html-теги, по умолчанию с тегами
     * 
     * @return string
     */
    public function getTitleActiveOrg($encode = false)
    {
        if ($encode) {
            return "{$this->organization->name} (договор № {$this->number})";
        }
        $org_name = Html::encode($this->organization->name);
        return "{$org_name} <span class=\"another-info-span\">(договор № {$this->number})</span>";
    }
}
