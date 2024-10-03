<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%departament}}`.
 */
class m240828_062856_add_new_phone_column_to_departament_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{%departament}}", "phone", $this->string(18));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%departament}}', 'phone');
    }
}
