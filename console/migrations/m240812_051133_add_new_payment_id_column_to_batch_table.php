<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%batch}}`.
 */
class m240812_051133_add_new_payment_id_column_to_batch_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{%batch}}", "payment_id", $this->integer(11));

        $this->createIndex('idx-batch-payment_id', 'batch', 'payment_id');
        $this->addForeignKey('fk-batch-payment_id', 'batch', 'payment_id', 'payment', 'id', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-batch-payment_id', 'batch');
        $this->dropIndex('idx-batch-payment_id', 'batch');

        $this->dropColumn('{{%batch}}', 'payment_id');
    }
}
