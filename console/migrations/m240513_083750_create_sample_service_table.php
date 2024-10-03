<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sample_service}}`.
 */
class m240513_083750_create_sample_service_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sample_service}}', [
            'id' => $this->primaryKey(),
            'sample_id' => $this->integer(11)->notNull(),
            'service_id' => $this->integer(11)->notNull(),
        ]);

        $this->createIndex('idx-sample_service-sample_id', 'sample_service', 'sample_id');
        $this->addForeignKey('fk-sample_service-sample_id', 'sample_service', 'sample_id', 'sample', 'id', 'RESTRICT');

        $this->createIndex('idx-sample_service-service_id', 'sample_service', 'service_id');
        $this->addForeignKey('fk-sample_service-service_id', 'sample_service', 'service_id', 'service', 'id', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-sample_service-sample_id','sample_service');
        $this->dropIndex('idx-sample_service-sample_id','sample_service');

        $this->dropForeignKey('fk-sample_service-service_id','sample_service');
        $this->dropIndex('idx-sample_service-service_id','sample_service');

        $this->dropTable('{{%sample_service}}');
    }
}
