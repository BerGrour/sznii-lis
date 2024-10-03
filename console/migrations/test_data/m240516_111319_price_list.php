<?php

use yii\db\Migration;

/**
 * Class m240516_111319_price_list
 */
class m240516_111319_price_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(<<<SQL
            INSERT INTO price_list (id, departament_id, research, price, status, period) VALUES
                (1, 4, "Полный анализ по 19 показателям", 1300.0, 1, 5),
                (2, 4, "Определение содержания НДК", 600.0, 1, 3),
                (3, 5, "Старое исследование молока", 500.0, 0, 2),
                (4, 5, "Исследование молока №1", 500.0, 1, 1),
                (5, 5, "Исследование молока №2", 400.0, 1, 1),
                (6, 6, "Анализ крови №1", 1000.0, 1, 2),
                (7, 6, "Анализ крови №2", 900.0, 1, 2),
                (8, 7, "ДНК", 800.0, 1, 3),
                (9, 7, "ИГ", 500.0, 1, 4);
            SQL
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('price_list');
    }
}