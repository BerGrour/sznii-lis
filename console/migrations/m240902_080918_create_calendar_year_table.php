<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%calendar_year}}`.
 */
class m240902_080918_create_calendar_year_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%calendar_year}}', [
            'id' => $this->primaryKey(),
            'number' => $this->integer()->notNull()->unique(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%calendar_year}}');
    }
}
