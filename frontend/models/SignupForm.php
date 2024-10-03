<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $type;
    public $staff_id;
    public $organization_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Это имя пользователя уже занято.'],
            ['username', 'string', 'min' => 4, 'max' => 32],
            ['username', 'validateLogin'],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 100],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Этот адрес электронной почты уже занят.'],

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],

            ['type', 'required'],

            ['staff_id', 'integer'],
            ['organization_id', 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'password' => 'Пароль',
            'type' => 'Для кого учетная запись?',
            'staff_id' => 'Сотрудник',
            'organization_id' => 'Организация',
        ];
    }

    public function validateLogin($attribute, $params, $validator)
    {
        $pattern = "/^[a-zA-Z][a-zA-Z0-9-_\.]+$/u";
        if (preg_match($pattern, $this->$attribute) == 0) {
            $this->addError($attribute, 'Имя пользователя может содержать только буквы, цифры и символы "-", "." и "_"');
        }
    }

    /**
     * Signs user up.
     *
     * @return bool|null whether the creating new account was successful and email was sent
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();

        if ($this->type == 'staff') {
            $user->staff_id = $this->staff_id;
            $user->organization_id = null;
        } elseif ($this->type == 'organization') {
            $user->organization_id = $this->organization_id;
            $user->staff_id = null;
        }

        return $user->save() && $this->sendEmail($user);
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}
