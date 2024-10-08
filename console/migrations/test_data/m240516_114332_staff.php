<?php

use yii\db\Migration;

/**
 * Class m240516_114332_staff
 */
class m240516_114332_staff extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(<<<SQL
            INSERT INTO staff (id, fio, job_id, employ_date, leave_date, phone) VALUES
                (2, "Администратор Админ Админович", 1, "2024-05-01", null, null),
                (3, "Регистрирующий Иван Иванович", 2, "2024-05-01", null, "+7 (911) 123-45-67"),
                (4, "Регистрация Петр Сменщик", 2, "2024-05-02", null, null),
                (5, "Главбухг Людмила Уволенная", 3, "2024-03-01", "2024-05-10", null),
                (6, "Главбухгалтер Валерия Новый", 3, "2024-05-10", null, null),
                (7, "Бухгалтер Дмитрий Первый", 4, "2024-05-11", null, null),
                (8, "Бухгалтер Анастасия Второй", 4, "2024-05-11", null, null),
                (9, "Руководитель Артем Кормов", 5, "2024-05-14", null, null),
                (10, "Лаборант Ян Кормов", 6, "2024-05-15", null, null),
                (11, "Лаборант Егор Кормов", 6, "2024-05-15", null, null),
                (12, "Практикант Евгений Кормов", 7, "2024-05-16", null, null),
                (13, "ГлавныйЛаб Валерий Молоков", 8, "2024-05-14", null, null),
                (14, "Ученый Даниил Молоков", 9, "2024-05-15", null, null),
                (15, "Интерн Сергей Молоков", 10, "2024-05-16", null, null),
                (16, "Интерн Александр Молоков", 10, "2024-05-16", null, null),
                (17, "Заместитель Валерий Биохимов", 11, "2024-05-14", null, null),
                (18, "Биохимик Даниил Биохимов", 12, "2024-05-15", null, null),
                (19, "Ученыйлаб Сергей Биохимов", 12, "2024-05-16", null, null),
                (20, "Ветеринар Наталья днк", 14, "2024-05-15", null, null),
                (21, "Подопытная крыса 1", 15, "2024-05-16", null, "+7 (911) 111-11-11"),
                (22, "Подопытная корова Маруся", 15, "2024-05-16", null, null),
                (23, "Подопытная корова Машка", 15, "2024-05-16", null, null),
                (24, "Подопытная крыса уволенная", 15, "2024-05-16", "2024-05-16", null);
            SQL
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('staff');
    }
}