<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%batch}}`.
 */
class m240513_083735_create_batch_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%batch}}', [
            'id' => $this->primaryKey(),
            'employed_at' => $this->dateTime()->notNull(),
            'staff_id' => $this->integer(11)->notNull(),
            'contract_id' => $this->integer(11)->notNull(),
        ]);

        $this->createIndex('idx-batch-staff_id', 'batch', 'staff_id');
        $this->addForeignKey('fk-batch-staff_id', 'batch', 'staff_id', 'staff', 'id', 'RESTRICT');

        $this->createIndex('idx-batch-contract_id', 'batch', 'contract_id');
        $this->addForeignKey('fk-batch-contract_id', 'batch', 'contract_id', 'contract', 'id', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-batch-staff_id','batch');
        $this->dropIndex('idx-batch-staff_id','batch');

        $this->dropForeignKey('fk-batch-contract_id','batch');
        $this->dropIndex('idx-batch-contract_id','batch');
    
        $this->dropTable('{{%batch}}');
    }
}
