<?php

namespace common\models;

use common\extensions\traits\BasicMethodsTrait;
use Yii;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "sample".
 *
 * @property int $id
 * @property string $identificator
 * @property int $num
 * @property int $departament_id
 * @property int $batch_id
 * @property int $busy
 * @property string $losted_at
 *
 * @property Departament $departament
 * @property Batch $batch
 * @property SampleService[] $sampleServices
 */
class Sample extends \yii\db\ActiveRecord
{
    use BasicMethodsTrait;

    public $lost_date;
    public $lost_time;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sample';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['identificator', 'num', 'departament_id', 'batch_id'], 'required'],
            [['identificator'], 'match', 'pattern' => '/^[А-ЯA-Z][0-9]{2}\.[0-9]{2}\.[0-9]{2}\.[0-9]{1,5}$/u', 'message' => '{attribute} должен иметь формат, как в примере: "К24.12.31.125"'],
            [['losted_at'], 'default', 'value' => NULL],
            [['num', 'departament_id', 'batch_id', 'busy'], 'integer'],
            [['losted_at'], 'safe'],
            [['identificator'], 'string', 'max' => 100],
            [['identificator'], 'unique'],
            [['departament_id'], 'exist', 'skipOnError' => true, 'targetClass' => Departament::class, 'targetAttribute' => ['departament_id' => 'id']],
            [['batch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Batch::class, 'targetAttribute' => ['batch_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'identificator' => 'Идентификатор',
            'num' => 'Порядковый номер',
            'departament_id' => 'Лаборатория',
            'batch_id' => 'Партия',
            'busy' => 'Занят',
            'losted_at' => 'Дата потери',

            'laboratory' => 'Лаборатория',
            'lost_date' => 'Дата',
            'lost_time' => 'Время',
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
     * Gets query for [[Batch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBatch()
    {
        return $this->hasOne(Batch::class, ['id' => 'batch_id']);
    }

    /**
     * Gets query for [[SampleServices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSampleServices()
    {
        return $this->hasMany(SampleService::class, ['sample_id' => 'id']);
    }

    /**
     * Получение заголовка
     * @param int $len ограничение длины
     * @return string
     */
    public function getShortTitle($len = 50)
    {
        $result = StringHelper::truncate($this->identificator, $len);
        return $result;
    }

    /**
     * Определяет порядковый номер пробы исходя из лаборатории и даты
     * 
     * @param string $employed_at дата принятия пробы (дефолт = $this->employed_at)
     * @param string $departament_id лаборатория в которую направлена проба (дефолт = $this->departament_id)
     * @param string $period Периодичность сброса порядкового номера
     * @return int порядковый номер
     */
    static function createNumber($employed_at, $departament_id, $period)
    {
        $year_month = Yii::$app->formatter->asDate($employed_at, "php:{$period}");
        $last_elem = self::find()->joinWith('batch')
            ->where(['departament_id' => $departament_id])
            ->andWhere(['LIKE', 'batch.employed_at', $year_month])
            ->orderBy(['id' => SORT_DESC])->one();

        $num = $last_elem ? (int)$last_elem->num + 1 : 1;

        return $num;
    }

    /**
     * Определяет и создает идентификатор пробы исходя из даты, лаборатории и порядкового номера
     * 
     * @param string $employed_at дата принятия пробы (дефолт = $this->employed_at)
     * @param string $abbreviation кодовая буква лаборатории (дефолт = $this->departament->abbreviation)
     * @param int $num порядковый номер (дефолт = $this->num)
     * @return string идентификатор, пример: "К24.05.08.125."
     */
    public function createIdentificator($employed_at, $abbreviation, $num)
    {
        $date = Yii::$app->formatter->asDate($employed_at, 'php:y.m.d.');
        $identificator = $abbreviation . $date . $num;
        return $identificator;
    }

    /**
     * Выводит статус потери пробы
     * 
     * @param int $service_id индекс исследования
     * @return string
     */
    public function getStatusLost($service_id)
    {
        if ($service_id) {
            $relation = $this->getSampleServices()
                ->where(['service_id' => $service_id])->one();
            if ($relation) {
                $completed_at = $relation->service->completed_at;

                if (!empty($completed_at) and $completed_at < $this->losted_at) {
                    return ' <span class="tooltip-custom tooltip-lost-after" data-toggle="tooltip" title="Проба была потеряна после завершения исследования">Потеряна, но числится</span>';
                }
            }
        }
        return ' <span class="warning-status">Потеряна</span>';
    }

    /**
     * Метод для получения списка исследований, в которых учавствует выбранная проба
     * 
     * @param int $sample_id индекс пробы
     * @param bool $linked кликабельные названия, default = false
     */
    public function getListServices($sample_id, $linked = false)
    {
        $list_services = [];
        $relations = SampleService::find()->where(['sample_id' => $sample_id])->all();

        foreach ($relations as $relation) {
            $service = Service::findOne(['id' => $relation->service_id]);
            $item = $linked ?
                $service->getLinkOnView(Html::encode($service->research))
                : Html::encode($service->research);

            array_push($list_services, $item);
        }
        return $list_services;
    }
}
