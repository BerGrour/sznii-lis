<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%job}}`.
 */
class m240513_063927_create_job_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%job}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'departament_id' => $this->integer(11)->notNull(),
        ]);

        $this->createIndex('idx-job-departament_id', 'job', 'departament_id');
        $this->addForeignKey('fk-job-departament_id', 'job', 'departament_id', 'departament', 'id', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-job-departament_id','job');
        $this->dropIndex('idx-job-departament_id','job');

        $this->dropTable('{{%job}}');
    }
}
