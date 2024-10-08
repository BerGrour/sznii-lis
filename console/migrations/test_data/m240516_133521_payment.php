<?php

use yii\db\Migration;

/**
 * Class m240516_133521_payment
 */
class m240516_133521_payment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(<<<SQL
            INSERT INTO payment (id, act_num, act_date, list_date, return_date, fact_sum, pay_date, file_act, file_act_client, file_pay, file_invoice, locked) VALUES
                (1, 1, "2024-01-02 10:18:35", "2024-01-03 08:18:35", "2024-01-05 16:18:35", 8600.0, "2024-01-04 16:18:35", 2, 3, 4, 5, 1),
                (2, 2, "2024-01-03 09:22:09", "2024-01-04 08:22:09", "2024-01-06 16:22:09", 28000.0, "2024-01-05 16:22:09", 2, 3, 4, 5, 1),
                (3, 3, "2024-05-21 09:50:08", "2024-05-21 10:50:08", "2024-05-23 09:50:08", 7700.0, "2024-05-22 09:50:08", 2, 3, 4, 5, 1),
                (4, 4, "2024-05-22 08:36:27", "2024-05-22 09:36:27", "2024-05-24 08:36:27", 9000.0, "2024-05-23 08:36:27", 2, 3, 4, 5, 1),
                (5, 5, "2024-05-22 15:57:40", "2024-05-22 16:57:40", "2024-05-24 15:57:40", 11600.0, "2024-05-23 15:57:40", 2, 3, 4, 5, 1),
                (6, 6, "2024-05-23 09:06:38", "2024-05-23 10:06:38", "2024-05-25 09:06:38", 3900.0, "2024-05-24 09:06:38", 2, 3, 4, 5, 1),
                (7, 7, "2024-05-23 09:26:29", "2024-05-23 10:26:29", "2024-05-26 09:26:29", 2500.0, "2024-05-25 09:26:29", 2, 3, 4, 5, 1),
                (8, 8, "2024-05-23 13:30:28", "2024-05-24 08:30:28", "2024-05-26 16:30:28", 10900.0, "2024-05-25 16:30:28", 2, 3, 4, 5, 1),
                (9, 9, "2024-05-24 13:00:13", "2024-05-24 13:51:29", "2024-05-26 12:21:30", 5400.0, "2024-05-25 12:21:30", 2, 3, 4, 5, 1);
            SQL
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('payment');
    }
}
