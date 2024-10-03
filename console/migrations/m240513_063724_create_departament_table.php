<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%departament}}`.
 */
class m240513_063724_create_departament_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%departament}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'role' => $this->string(100)->notNull(),
            'short_name' => $this->string(20),
            'abbreviation' => $this->string(10),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%departament}}');
    }
}
