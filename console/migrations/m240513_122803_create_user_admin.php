<?php

use common\models\User;
use yii\db\Migration;

/**
 * Class m240513_122803_create_user_admin
 */
class m240513_122803_create_user_admin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(<<<SQL
            INSERT INTO departament (id, title, role, short_name, abbreviation) VALUES
                (1, "Управление", "admin", null, null);

            INSERT INTO job (id, title, departament_id) VALUES
                (1, "Администратор", 1);

            INSERT INTO staff (id, fio, job_id, employ_date, leave_date, phone) VALUES
                (1, "Программист", 1, "2024-05-01", null, null);
            SQL
        );

        $user = new User();
        $user->username = '***************';
        $user->email = '***************';
        $user->setPassword('***************');
        $user->generateAuthKey();
        $user->staff_id = 1;
        $user->save(false);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $user = User::findByUsername('***************');
        if ($user) {
            $user->delete();
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240408_082101_create_user_admin cannot be reverted.\n";

        return false;
    }
    */
}
