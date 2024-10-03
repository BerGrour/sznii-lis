<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%calendar_date}}`.
 */
class m240902_081057_create_calendar_date_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%calendar_date}}', [
            'id' => $this->primaryKey(),
            'year_id' => $this->integer()->notNull(),
            'date' => $this->string()->notNull(),
        ]);

        $this->createIndex('idx-calendar_date-year_id', 'calendar_date', 'year_id');
        $this->addForeignKey('fk-calendar_date-year_id', 'calendar_date', 'year_id', 'calendar_year', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-calendar_date-year_id', 'calendar_date');
        $this->dropIndex('idx-calendar_date-year_id', 'calendar_date');

        $this->dropTable('{{%calendar_date}}');
    }
}
