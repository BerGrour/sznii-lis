<?php

namespace common\models;

use common\extensions\traits\BasicMethodsTrait;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "organization".
 *
 * @property int $id
 * @property string $name
 * @property string $inn
 * @property string|null $address
 * @property int|null $phone
 * @property string|null $email
 * @property string|null $director
 * @property string|null $comment
 *
 * @property Contract[] $contracts
 * @property User $user
 */
class Organization extends \yii\db\ActiveRecord
{
    use BasicMethodsTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'organization';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'inn'], 'required'],
            [['address', 'phone', 'email', 'director', 'comment'], 'default', 'value' => NULL],
            [['phone'], 'match', 'pattern' => '/^\+7 \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}$/'],
            [['name', 'inn', 'address', 'director'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['comment'], 'string', 'max' => 3000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'inn' => 'ИНН',
            'address' => 'Адрес',
            'phone' => 'Телефон',
            'email' => 'email',
            'director' => 'Директор',
            'comment' => 'Примечание',

            'active_contract' => 'Активный договор'
        ];
    }

    /**
     * Gets query for [[Contracts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContracts()
    {
        return $this->hasMany(Contract::class, ['organization_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['organization_id' => 'id']);
    }

    /**
     * Получение заголовка
     * @param int $len ограничение длины
     * @return string
     */
    public function getShortTitle($len = 50, $encode = true)
    {
        $result = StringHelper::truncate($this->name, $len);
        if ($encode) return Html::encode($result);
        return $result;
    }

    /**
     * Метод для получения организаций с активным договором
     * @return ActiveQuery
     */
    public static function getActiveOrganizations()
    {
        $result = self::find()->joinWith('contracts')
            ->where(['IN', 'organization.id', Contract::getActiveContracts()->select('organization_id')]);

        return $result;
    }

    /**
     * Метод для получения активного договора соответствующей организации
     * 
     * @return \common\models\Contract
     */
    public function getActiveContract()
    {
        $cur_date = date('Y-m-d');
        $contract = Contract::find()->where(['organization_id' => $this->id])
            ->andWhere(['<=', 'start_date', $cur_date])
            ->andWhere(['>=', 'end_date', $cur_date])->one();
        return $contract;
    }

    /**
     * Метод для вывода информации по договору организаций
     * 
     * @return string
     */
    public function getInfoAboutContract()
    {
        $contract = $this->getActiveContract();

        if ($contract) {
            $start = Yii::$app->formatter->asDatetime($contract->start_date, 'php:d.m.y');
            $end = Yii::$app->formatter->asDatetime($contract->end_date, 'php:d.m.y');
            $content = "{$start} - {$end}";

            // Подсчет разницы даты окончания и текущей, чтобы оповестить
            // о скором прекращении договора

        } else {
            return '<span class="warning-status">Действующего договора нет<span>';
        }
        return $content;
    }
}
