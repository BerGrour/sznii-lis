<?php

use yii\db\Migration;

/**
 * Class m240903_101200_add_rbac_permissions
 */
class m240903_101200_add_rbac_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $calendarSee = $auth->createPermission('calendar/see');
        $calendarSee->description = 'Просмотр календаря';
        $auth->add($calendarSee);

        $calendarCreate = $auth->createPermission('calendar/create');
        $calendarCreate->description = 'Создание календаря';
        $auth->add($calendarCreate);

        $calendarUpdate = $auth->createPermission('calendar/update');
        $calendarUpdate->description = 'Изменение календаря';
        $auth->add($calendarUpdate);

        $serviceSeeLate = $auth->createPermission('service/seeLate');
        $serviceSeeLate->description = 'Просмотр просроченных исследований';
        $auth->add($serviceSeeLate);

        $admin = $auth->getRole('admin');
        $auth->addChild($admin, $calendarCreate);
        $auth->addChild($admin, $calendarUpdate);
        $auth->addChild($admin, $serviceSeeLate);

        $booker = $auth->getRole('booker');
        $auth->addChild($booker, $calendarSee);

        $laboratory = $auth->getRole('laboratory');
        $auth->addChild($laboratory, $calendarSee);
        $auth->addChild($laboratory, $serviceSeeLate);

        $registration = $auth->getRole('registration');
        $auth->addChild($registration, $calendarSee);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $calendarSee = $auth->getPermission('calendar/see');
        $calendarCreate = $auth->getPermission('calendar/create');
        $calendarUpdate = $auth->getPermission('calendar/update');
        $serviceSeeLate = $auth->getPermission('service/seeLate');

        $admin = $auth->getRole('admin');
        $auth->removeChild($admin, $calendarCreate);
        $auth->removeChild($admin, $calendarUpdate);
        $auth->removeChild($admin, $serviceSeeLate);

        $booker = $auth->getRole('booker');
        $auth->removeChild($booker, $calendarSee);
        
        $laboratory = $auth->getRole('laboratory');
        $auth->removeChild($laboratory, $calendarSee);
        $auth->removeChild($laboratory, $serviceSeeLate);
        
        $registration = $auth->getRole('registration');
        $auth->removeChild($registration, $calendarSee);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240903_101200_add_rbac_permissions cannot be reverted.\n";

        return false;
    }
    */
}
