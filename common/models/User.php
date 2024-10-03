<?php

namespace common\models;

use common\extensions\traits\BasicMethodsTrait;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\StringHelper;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property int $organization_id
 * @property int $staff_id
 * 
 * @property Organization $organization
 * @property Staff $staff
 */
class User extends ActiveRecord implements IdentityInterface
{
    use BasicMethodsTrait;
    const STATUS_DELETED = 0;
    const STATUS_DELETED_NAME = 'Удален';
    const STATUS_INACTIVE = 9;
    const STATUS_INACTIVE_NAME = 'Не активирован';
    const STATUS_ACTIVE = 10;
    const STATUS_ACTIVE_NAME = 'Активен';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
            [
                'staff_id',
                'required',
                'message' => 'Требуется обязательно указать либо сотрудника либо организацию',
                'when' => function ($model) {
                    return empty($model->organization_id);
                }
            ],
            [
                'organization_id',
                'required',
                'message' => 'Требуется обязательно указать либо сотрудника либо организацию',
                'when' => function ($model) {
                    return empty($model->staff_id);
                }
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
            'username' => 'Логин',
            'staff_id' => 'Сотрудник',
            'organization_id' => 'Организация',
            'status' => 'Статус',
            'created_at' => 'Создан',
            'updated_at' => 'Изменен',
            'role' => 'Роль'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username, $active = true)
    {
        if ($active) {
            return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
        } else {
            return static::findOne(['username' => $username]);
        }
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
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
     * Gets query for [[Organization]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::class, ['id' => 'organization_id']);
    }

    /**
     * Получение статуса
     * @return string|null
     */
    public function getStatusName()
    {
        switch ($this->status) {
            case 0:
                return '<span class="warning-status">' . self::STATUS_DELETED_NAME . '</span>';
            case 9:
                return '<span class="inactive-status">' . self::STATUS_INACTIVE_NAME . '</span>';
            case 10:
                return '<span class="active-status">' . self::STATUS_ACTIVE_NAME . '</span>';
        }
    }

    /**
     * Получение заголовка
     * @param int $len ограничение длины
     * @return string
     */
    public function getShortTitle($len = 50)
    {
        $result = StringHelper::truncate($this->username, $len);
        return $result;
    }

    /**
     * Метод для получения наименования роли и описание её в подсказке
     * @param string $role_name
     * @return string
     */
    static function getRoleInfo($role_name)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($role_name);
        return "<span class=\"tooltip-custom\" data-toggle=\"tooltip\" title=\"{$role->data}\">{$role->description}</span>";
    }

    /** 
     * Получение наименования роли пользователя
     * 
     * @return string
     */
    public function getRole()
    {
        $auth = Yii::$app->authManager;
        $roles = $auth->getRolesByUser($this->id);

        $descriptions = [];

        foreach ($roles as $role) {
            $descriptions[] = self::getRoleInfo($role->name);
        }

        return implode(', ', $descriptions);
    }

    static function getListRoles()
    {
        $auth = Yii::$app->authManager;
        $roles = $auth->getRoles();

        foreach ($roles as &$role) {
            if ($role->name != 'client') {
                $role->description = self::getRoleInfo($role->name);
            } else {
                unset($roles[$role->name]);
            }
        }
        return $roles;
    }
}
