<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user}}`.
 */
class m240513_100611_add_new_columns_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'organization_id', $this->integer(11));
        $this->addColumn('{{%user}}', 'staff_id', $this->integer(11));

        $this->createIndex('idx-user-organization_id', 'user', 'organization_id');
        $this->addForeignKey('fk-user-organization_id', 'user', 'organization_id', 'organization', 'id', 'RESTRICT');

        $this->createIndex('idx-user-staff_id', 'user', 'staff_id');
        $this->addForeignKey('fk-user-staff_id', 'user', 'staff_id', 'staff', 'id', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-user-organization_id','user');
        $this->dropIndex('idx-user-organization_id','user');

        $this->dropForeignKey('fk-user-staff_id','user');
        $this->dropIndex('idx-user-staff_id','user');

        $this->dropColumn('{{%user}}', 'organization_id');
        $this->dropColumn('{{%user}}', 'staff_id');
    }
}
