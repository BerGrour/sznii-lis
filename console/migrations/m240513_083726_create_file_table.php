<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%file}}`.
 */
class m240513_083726_create_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%file}}', [
            'id' => $this->primaryKey(),
            'filepath' => $this->string(255)->notNull(),
            'filename' => $this->string(255),
            'filesize' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%file}}');
    }
}
