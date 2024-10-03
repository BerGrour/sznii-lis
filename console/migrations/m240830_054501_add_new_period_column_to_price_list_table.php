<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%price_list}}`.
 */
class m240830_054501_add_new_period_column_to_price_list_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{%price_list}}", "period", $this->integer(11)->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%price_list}}', 'period');
    }
}
