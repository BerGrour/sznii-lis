<?php

use yii\db\Migration;

/**
 * Class m240516_132853_files
 */
class m240516_132853_files extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(<<<SQL
            INSERT INTO file (id, filepath, filename, filesize, organization_id) VALUES
            (1, "protected/uploads/service/results/1720000000.svg", "test_results.svg", 100, null),
            (2, "protected/uploads/payment/acts/1720000000.svg", "test_act.svg", 100, null),
            (3, "protected/uploads/payment/clients_acts/1720000000.svg", "test_act_client.svg", 100, null),
            (4, "protected/uploads/payment/pay_docs/1720000000.svg", "test_pay.svg", 100, null),
            (5, "protected/uploads/payment/invoices/1720000000.svg", "test_invoice.svg", 100, null);
            SQL
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('file');
    }
}
