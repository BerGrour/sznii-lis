<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%staff}}`.
 */
class m240513_075829_create_staff_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%staff}}', [
            'id' => $this->primaryKey(),
            'fio' => $this->string(255)->notNull(),
            'job_id' => $this->integer(11)->notNull(),
            'employ_date' => $this->date()->notNull(),
            'leave_date' => $this->date(),
            'phone' => $this->string(18),
        ]);

        $this->createIndex('idx-staff-job_id', 'staff', 'job_id');
        $this->addForeignKey('fk-staff-job_id', 'staff', 'job_id', 'job', 'id', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-staff-job_id','staff');
        $this->dropIndex('idx-staff-job_id','staff');

        $this->dropTable('{{%staff}}');
    }
}
