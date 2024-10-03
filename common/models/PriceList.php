<?php

namespace common\models;

use common\extensions\traits\BasicMethodsTrait;
use Yii;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "price_list".
 *
 * @property int $id
 * @property int $departament_id
 * @property string $research
 * @property float $price
 * @property int $status
 * @property int $period
 *
 * @property ArchivePriceList[] $archivePriceLists
 * @property Departament $departament
 */
class PriceList extends \yii\db\ActiveRecord
{
    use BasicMethodsTrait;
    const STATUS_INACTIVE = 'Не активно';
    const STATUS_ACTIVE = 'Активно';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'price_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['research', 'price', 'departament_id'], 'required'],
            [['research'], 'unique'],
            [['price'], 'number'],
            [['status', 'departament_id', 'period'], 'integer'],
            [['research'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'departament_id' => 'Лаборатория',
            'research' => 'Наименование исследования',
            'price' => 'Цена, в руб.',
            'status' => 'Статус',
            'period' => 'Дней на выполнение'
        ];
    }

    /**
     * @inheritDoc
     * Для сохранения данных в архив при изменении цены исследования
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (!$insert) {
            if (isset($changedAttributes['price']) and $changedAttributes['price'] != $this->price) {

                $historyModel = new ArchivePriceList();

                $historyModel->research_id = $this->id;
                $historyModel->price = $changedAttributes['price'];
                $historyModel->updated_at = date('Y-m-d H:i:s');
                $historyModel->save(false);
            }
        }
    }

    /**
     * Gets query for [[ArchivePriceLists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArchivePriceLists()
    {
        return $this->hasMany(ArchivePriceList::class, ['research_id' => 'id']);
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
     * Получение заголовка
     * @param int $len ограничение длины
     * @return string
     */
    public function getShortTitle($len = 50)
    {
        $result = StringHelper::truncate(Html::encode($this->research), $len);
        return $result;
    }

    /**
     * Возвращает наименование вида для отдела
     * 
     * @return string
     */
    public function getStatusName()
    {
        return $this->status ? self::STATUS_ACTIVE : self::STATUS_INACTIVE;
    }

    /**
     * Получение списка активных услуг-исследований для лаборатории
     * @return array
     */
    public static function getListLaboratoryResearches($laboratory_id)
    {
        return self::find()->where([
            'departament_id' => $laboratory_id,
            'status' => 1
        ])->all();
    }
}
