<?php

namespace common\models;

use common\extensions\traits\BasicMethodsTrait;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "departament".
 *
 * @property int $id
 * @property string $title
 * @property int $role
 * @property string|null $phone
 * @property string|null $short_name
 * @property string|null $abbreviation
 * @property string|null $period
 * 
 *
 * @property Job[] $jobs
 * @property Sample[] $samples
 * @property PriceList[] $price_lists
 */
class Departament extends \yii\db\ActiveRecord
{
    use BasicMethodsTrait;
    const ROLE_ADMIN = 'Администратор';
    const ROLE_BOOKER = 'Бухгалтер';
    const ROLE_LABORATORY = 'Лаборатория';
    const ROLE_REGISTRATION = 'Регистратура';
    const PERIOD_YEAR = 'Y';
    const PERIOD_MONTH = 'Y-m';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'departament';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'role'], 'required'],
            [['phone', 'short_name', 'abbreviation', 'period'], 'default', 'value' => NULL],
            [['role'], 'string', 'max' => 100],
            [['title'], 'string', 'max' => 255],
            [['short_name'], 'string', 'max' => 20],
            [['abbreviation'], 'string', 'max' => 1],
            [['period'], 'string', 'max' => 10],
            [['phone'], 'safe'],
            [['phone'], 'match', 'pattern' => '/^\+7 \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}$/'],
            [
                ['short_name', 'abbreviation', 'phone', 'period'],
                'required',
                'when' => function ($model) {
                    return $model->role === 'laboratory';
                },
                'whenClient' => 'function(attribute, value) {
                    return $("#short_name-field").is(":visible")
                        && $("#abbreviation-field").is(":visible")
                        && $("#period-field").is(":visible");
                }'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role' => 'Роль сотрудников',
            'title' => 'Название',
            'roleName' => 'Роль сотрудников',
            'short_name' => 'Вид проб',
            'abbreviation' => 'Аббревиатура',
            'phone' => 'Телефон ответственного',
            'period' => 'Периодичность обновления',
            'periodName' => 'Периодичность обновления',
        ];
    }

    /**
     * Gets query for [[Jobs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJobs()
    {
        return $this->hasMany(Job::class, ['departament_id' => 'id']);
    }

    /**
     * Gets query for [[Samples]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSamples()
    {
        return $this->hasMany(Sample::class, ['departament_id' => 'id']);
    }

    /**
     * Gets query for [[PriceList]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPriceLists()
    {
        return $this->hasMany(PriceList::class, ['departament_id' => 'id']);
    }

    /**
     * Возвращает наименование вида для отдела
     * @return string|null
     */
    public function getRoleName()
    {
        $role = match ($this->role) {
            'admin' => self::ROLE_ADMIN,
            'booker' => self::ROLE_BOOKER,
            'laboratory' => self::ROLE_LABORATORY,
            'registration' => self::ROLE_REGISTRATION
        };
        return $role;
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
     * Возвращает список лабораторий ['id' => $attribute]
     * @param $attribute выводимый атрибут
     * @return array
     */
    public static function getLaboratoriesList($attribute, $lab_id = null)
    {
        $labs = Departament::find()
            ->where(['role' => 'laboratory'])
            ->andFilterWhere(['id' => $lab_id])
            ->all();
        $laboratories_list = ArrayHelper::map(
            $labs,
            'id',
            $attribute
        );
        return $laboratories_list;
    }

    /**
     * Возвращает наименование периодичности
     * @return string|null
     */
    public function getPeriodName()
    {
        if ($this->period) {
            $period = match ($this->period) {
                self::PERIOD_YEAR => 'Год',
                self::PERIOD_MONTH => 'Месяц',
            };
            return $period;
        }
    }
}
