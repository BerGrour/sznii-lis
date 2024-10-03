<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%archive_price_list}}`.
 */
class m240513_110611_create_archive_price_list_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%archive_price_list}}', [
            'id' => $this->primaryKey(),
            'research_id' => $this->integer(11)->notNull(),
            'price' => $this->float()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ]);

        $this->createIndex('idx-archive_price_list-research_id', 'archive_price_list', 'research_id');
        $this->addForeignKey('fk-archive_price_list-research_id', 'archive_price_list', 'research_id', 'price_list', 'id', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-archive_price_list-research_id','archive_price_list');
        $this->dropIndex('idx-archive_price_list-research_id','archive_price_list');
    
        $this->dropTable('{{%archive_price_list}}');
    }
}
