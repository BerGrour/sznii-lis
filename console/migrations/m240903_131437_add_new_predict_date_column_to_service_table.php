<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%service}}`.
 */
class m240903_131437_add_new_predict_date_column_to_service_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{%service}}", "predict_date", $this->date()->after("staff_id"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%service}}', 'predict_date');
    }
}
