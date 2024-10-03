<?php

use yii\db\Migration;

/**
 * Class m240516_124913_contract
 */
class m240516_124913_contract extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(<<<SQL
            INSERT INTO contract (id, number, organization_id, start_date, end_date, list_date) VALUES
                (1, 1, 1, "2023-01-01", "2024-01-01", "2023-03-25"),
                (2, 2, 2, "2024-01-01", "2025-01-01", null),
                (3, 3, 1, "2024-01-01", "2025-01-01", null),
                (4, 4, 3, "2024-01-01", "2025-01-01", "2024-05-16");
            SQL
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('contract');
    }
}