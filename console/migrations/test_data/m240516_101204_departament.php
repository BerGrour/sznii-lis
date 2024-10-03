<?php

use yii\db\Migration;

/**
 * Class m240516_101204_departament
 */
class m240516_101204_departament extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(<<<SQL
            INSERT INTO departament (id, title, role, short_name, abbreviation, phone, period) VALUES
                (2, "Регистратура", "registration", null, null, null, null),
                (3, "Бухгалтерия", "booker", null, null, null, null),
                (4, "Лаборатория с кормом", "laboratory", "Корм", "К", null, 'Y'),
                (5, "Лаборатория с молоком", "laboratory", "Молоко", "М", null, 'Y-m'),
                (6, "Лаборатория биохимии", "laboratory", "Биохимия", "Б", null, 'Y-m'),
                (7, "Лаборатория ДНК", "laboratory", "ДНК", "Д", null, 'Y');
            SQL
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('departament');
    }
}
