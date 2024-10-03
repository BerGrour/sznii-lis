<?php

namespace common\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property string $filepath
 * @property string|null $filename
 * @property integer|null $filesize
 * @property integer|null $organization_id
 * @property integer|null $departament_id
 *
 * @property Payment $payment_act
 * @property Payment $payment_act_client
 * @property Payment $payment_pay
 * @property Payment $payment_invoice
 * @property Service $service
 * @property Organization $organization
 * @property Departament $departament
 */
class File extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['filepath'], 'required'],
            [['filepath', 'filename'], 'string', 'max' => 255],
            [['filesize'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filepath' => 'Путь к файлу',
            'filename' => 'Название файла',
            'filesize' => 'Размер файла'
        ];
    }

    /**
     * Gets query for [[Payment_act]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayment_act()
    {
        return $this->hasOne(Payment::class, ['file_act' => 'id']);
    }

    /**
     * Gets query for [[Payment_act_client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayment_act_client()
    {
        return $this->hasOne(Payment::class, ['file_act_client' => 'id']);
    }

    /**
     * Gets query for [[Payment_pay]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayment_pay()
    {
        return $this->hasOne(Payment::class, ['file_pay' => 'id']);
    }

    /**
     * Gets query for [[Payment_invoice]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayment_invoice()
    {
        return $this->hasOne(Payment::class, ['file_invoice' => 'id']);
    }

    /**
     * Gets query for [[Services]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServices()
    {
        return $this->hasOne(Service::class, ['file_id' => 'id']);
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
     * Gets query for [[Departament]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepartament()
    {
        return $this->hasOne(Departament::class, ['id' => 'departament_id']);
    }

    /**
     * Получение относительного пути к файлу по URL
     * @return string
     */
    public function getPathLink()
    {
        $content = Yii::$app->basePath . '/' . $this->filepath;
        return $content;
    }

    /**
     * Получение ссылки на action по проверке доступа и открытию файла
     */
    public function getUrlFile()
    {
        $content = Url::to(['file/document', 'id' => $this->id]);
        return $content;
    }

    /**
     * Проверка доступа к файлу
     * @return bool
     */
    public function hasAccess()
    {
        $identity = Yii::$app->user->identity;
        if (
            (Yii::$app->user->can('seeFiles'))
            or ($identity->organization_id == $this->organization_id)
            or (($identity->staff_id)
                and (Staff::findOne(['id' => $identity->staff_id])->job->departament_id == $this->departament_id)
            )
        ) {
            return true;
        }
        return false;
    }
}
