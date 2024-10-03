<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%organization}}`.
 */
class m240513_081618_create_organization_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%organization}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'inn' => $this->string(255)->notNull(),
            'address' => $this->string(255),
            'phone' => $this->string(18),
            'email' => $this->string(255),
            'director' => $this->string(255),
            'comment' => $this->string(3000),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%organization}}');
    }
}
