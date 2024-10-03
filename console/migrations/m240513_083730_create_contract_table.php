<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%contract}}`.
 */
class m240513_083730_create_contract_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%contract}}', [
            'id' => $this->primaryKey(),
            'number' => $this->integer()->notNull(),
            'organization_id' => $this->integer(11)->notNull(),
            'start_date' => $this->date()->notNull(),
            'end_date' => $this->date()->notNull(),
            'list_date' => $this->date(),
        ]);

        $this->createIndex('idx-contract-organization_id', 'contract', 'organization_id');
        $this->addForeignKey('fk-contract-organization_id', 'contract', 'organization_id', 'organization', 'id', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-contract-organization_id','contract');
        $this->dropIndex('idx-contract-organization_id','contract');

        $this->dropTable('{{%contract}}');
    }
}
