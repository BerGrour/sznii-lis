<?php

use yii\db\Migration;

/**
 * Class m240516_123422_organization
 */
class m240516_123422_organization extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(<<<SQL
            INSERT INTO organization (id, name, inn, address, phone, email, director, comment) VALUES
                (1, 'ООО "АЛЬТА ДЖЕНЕТИКС РАША"', 123456789, "г. Вологда, ...", "+7 (912) 345-67-89", "alta-djenetix-russia@org.com", "Адрей Альта Дженетикс", "Комментарий"),
                (2, 'СХПК ПЗ "Майский"', 987654321, "г. Вологда, Молочное", null, "mayskiy@vologda-city.ru", null, null),
                (3, 'ОАО "Заря"', 147258369, null, null, null, null, "без почты");
            SQL
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('organization');
    }
}