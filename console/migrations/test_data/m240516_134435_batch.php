<?php

use yii\db\Migration;

/**
 * Class m240516_134435_batch
 */
class m240516_134435_batch extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(<<<SQL
            INSERT INTO batch (id, employed_at, staff_id, contract_id, payment_id) VALUES
            (1, "2023-01-01 08:00:00", 3, 1, 1),
            (2, "2023-01-02 10:00:00", 3, 1, 2),

            (3, "2024-05-20 09:10:00", 4, 2, 3),
            (4, "2024-05-20 11:52:15", 4, 4, 4),

            (5, "2024-05-21 10:14:51", 4, 4, 5),
            (6, "2024-05-21 13:05:17", 4, 3, 6),
            (7, "2024-05-21 13:33:58", 4, 4, 7),

            (8, "2024-05-22 08:49:24", 3, 2, 8),
            (9, "2024-05-22 12:19:01", 3, 3, 9),

            (10, "2024-05-25 15:01:41", 4, 2, null);
            SQL
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('batch');
    }

}
