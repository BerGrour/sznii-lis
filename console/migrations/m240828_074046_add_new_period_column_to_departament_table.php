<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%departament}}`.
 */
class m240828_074046_add_new_period_column_to_departament_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{%departament}}", "period", $this->string(10));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%departament}}', 'period');
    }
}
