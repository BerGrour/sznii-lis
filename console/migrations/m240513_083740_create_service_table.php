<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%service}}`.
 */
class m240513_083740_create_service_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%service}}', [
            'id' => $this->primaryKey(),
            'batch_id' => $this->integer(11)->notNull(),
            'research' => $this->string(255)->notNull(),
            'price' => $this->float()->notNull(),
            'started_at' => $this->dateTime()->notNull(),
            'staff_id' => $this->integer(11)->notNull(),
            'pre_sum' => $this->float(),
            'completed_at' => $this->dateTime(),
            'payment_id' => $this->integer(11),
            'file_id' => $this->integer(11),
        ]);

        $this->createIndex('idx-service-batch_id', 'service', 'batch_id');
        $this->addForeignKey('fk-service-batch_id', 'service', 'batch_id', 'batch', 'id', 'RESTRICT');

        $this->createIndex('idx-service-staff_id', 'service', 'staff_id');
        $this->addForeignKey('fk-service-staff_id', 'service', 'staff_id', 'staff', 'id', 'RESTRICT');

        $this->createIndex('idx-service-payment_id', 'service', 'payment_id');
        $this->addForeignKey('fk-service-payment_id', 'service', 'payment_id', 'payment', 'id', 'RESTRICT');

        $this->createIndex('idx-service-file_id', 'service', 'file_id');
        $this->addForeignKey('fk-service-file_id', 'service', 'file_id', 'file', 'id', 'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-service-batch_id','service');
        $this->dropIndex('idx-service-batch_id','service');

        $this->dropForeignKey('fk-service-staff_id','service');
        $this->dropIndex('idx-service-staff_id','service');

        $this->dropForeignKey('fk-service-payment_id','service');
        $this->dropIndex('idx-service-payment_id','service');

        $this->dropForeignKey('fk-service-file_id','service');
        $this->dropIndex('idx-service-file_id','service');

        $this->dropTable('{{%service}}');
    }
}
