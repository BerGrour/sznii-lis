<?php

use yii\db\Migration;

/**
 * Class m240516_113710_job
 */
class m240516_113710_job extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(<<<SQL
            INSERT INTO job (id, title, departament_id) VALUES
                (2, "Приём проб", 2),
                (3, "Главный бухгалтер", 3),
                (4, "Бухгалтер", 3),
                (5, "Руководитель лаборатории", 4),
                (6, "Лаборант", 4),
                (7, "Практикант", 4),
                (8, "Главный лаборант", 5),
                (9, "Ученый", 5),
                (10, "Интерн", 5),
                (11, "Заместитель", 6),
                (12, "Биохимик", 6),
                (13, "Ученый-лаборант", 7),
                (14, "Ветеринар", 7),
                (15, "Подопытный", 7);
            SQL
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('job');
    }
}