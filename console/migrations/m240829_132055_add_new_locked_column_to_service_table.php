<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%service}}`.
 */
class m240829_132055_add_new_locked_column_to_service_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{%service}}", "locked", $this->smallInteger(6)->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%service}}', 'locked');
    }
}
