<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%price_list}}`.
 */
class m240513_085517_create_price_list_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%price_list}}', [
            'id' => $this->primaryKey(),
            'departament_id' => $this->integer(11)->notNull(),
            'research' => $this->string(255)->notNull()->unique(),
            'price' => $this->float()->notNull(),
            'status' => $this->smallInteger(6)->notNull()->defaultValue(1),
        ]);

        $this->createIndex('idx-price_list-departament_id', 'price_list', 'departament_id');
        $this->addForeignKey('fk-price_list-departament_id', 'price_list', 'departament_id', 'departament', 'id', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-price_list-departament_id','price_list');
        $this->dropIndex('idx-price_list-departament_id','price_list');

        $this->dropTable('{{%price_list}}');
    }
}
