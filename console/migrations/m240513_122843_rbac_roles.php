<?php

use common\models\User;
use yii\db\Migration;

/**
 * Class m240513_122843_rbac_roles
 */
class m240513_122843_rbac_roles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $admin = $auth->getRole('admin');

        $registration = $auth->createRole('registration');
        $registration->description = 'Регистратура';
        $registration->data = 'Приём проб; ввод данных об организациях; оформление договоров; отслеживание проб.';
        $auth->add($registration);

        $laboratory = $auth->createRole('laboratory');
        $laboratory->description = 'Лаборатория';
        $laboratory->data = 'Исследование проб в рамках своей лаборатории; загрузка результатов исследований.';
        $auth->add($laboratory);

        $booker = $auth->createRole('booker');
        $booker->description = 'Бухгалтер';
        $booker->data = 'Выставление счетов и актов; контроль оплаты и отчетности; корректировка стоимости исследований.';
        $auth->add($booker);

        $client = $auth->createRole('client');
        $client->data = 'Доступ к информации по пробам и исследованиям в рамках своей организации; прикрепление пописанных документов.';
        $client->description = 'Заказчик';
        $auth->add($client);

        // Создание разрешений

        // User (пользователи)
        $userSee = $auth->createPermission('user/see');
        $userSee->description = 'Просмотр пользователей';
        $auth->add($userSee);

        $userCreate = $auth->createPermission('user/create');
        $userCreate->description = 'Создание пользователя';
        $auth->add($userCreate);

        $userUpdate = $auth->createPermission('user/update');
        $userUpdate->description = 'Редактирование пользователя';
        $auth->add($userUpdate);

        $userDelete = $auth->createPermission('user/delete');
        $userDelete->description = 'Удаление пользователя';
        $auth->add($userDelete);

        // Управление ролями между пользователями
        $manageRoles = $auth->createPermission('manageRoles');
        $manageRoles->description = 'Управление ролями между пользователями';
        $auth->add($manageRoles);

        // departament (отделы)
        $departamentSee = $auth->createPermission('departament/see');
        $departamentSee->description = 'Просмотр отделов';
        $auth->add($departamentSee);

        $departamentCreate = $auth->createPermission('departament/create');
        $departamentCreate->description = 'Создание отдела';
        $auth->add($departamentCreate);

        $departamentUpdate = $auth->createPermission('departament/update');
        $departamentUpdate->description = 'Редактирование отдела';
        $auth->add($departamentUpdate);

        $departamentDelete = $auth->createPermission('departament/delete');
        $departamentDelete->description = 'Удаление отдела';
        $auth->add($departamentDelete);
        
        // job (отделы)
        $jobSee = $auth->createPermission('job/see');
        $jobSee->description = 'Просмотр должностей';
        $auth->add($jobSee);

        $jobCreate = $auth->createPermission('job/create');
        $jobCreate->description = 'Создание должности';
        $auth->add($jobCreate);

        $jobUpdate = $auth->createPermission('job/update');
        $jobUpdate->description = 'Редактирование должности';
        $auth->add($jobUpdate);

        $jobDelete = $auth->createPermission('job/delete');
        $jobDelete->description = 'Удаление должности';
        $auth->add($jobDelete);

        // contract (договор)
        $contractSee = $auth->createPermission('contract/see');
        $contractSee->description = 'Просмотр договоров';
        $auth->add($contractSee);

        $contractCreate = $auth->createPermission('contract/create');
        $contractCreate->description = 'Создание договора';
        $auth->add($contractCreate);

        $contractUpdate = $auth->createPermission('contract/update');
        $contractUpdate->description = 'Редактирование договора';
        $auth->add($contractUpdate);

        $contractDelete = $auth->createPermission('contract/delete');
        $contractDelete->description = 'Удаление договора';
        $auth->add($contractDelete);

        // organization (организации)
        $organizationSee = $auth->createPermission('organization/see');
        $organizationSee->description = 'Просмотр организаций';
        $auth->add($organizationSee);

        $organizationCreate = $auth->createPermission('organization/create');
        $organizationCreate->description = 'Создание организации';
        $auth->add($organizationCreate);

        $organizationUpdate = $auth->createPermission('organization/update');
        $organizationUpdate->description = 'Редактирование организации';
        $auth->add($organizationUpdate);

        $organizationDelete = $auth->createPermission('organization/delete');
        $organizationDelete->description = 'Удаление организации';
        $auth->add($organizationDelete);
        
        // payment (оплата)
        $paymentSee = $auth->createPermission('payment/see');
        $paymentSee->description = 'Просмотр оплат';
        $auth->add($paymentSee);

        $paymentCreate = $auth->createPermission('payment/create');
        $paymentCreate->description = 'Создание оплаты';
        $auth->add($paymentCreate);

        $paymentUpdate = $auth->createPermission('payment/update');
        $paymentUpdate->description = 'Редактирование оплаты';
        $auth->add($paymentUpdate);

        $paymentClientUpdate = $auth->createPermission('payment/clientUpdate');
        $paymentClientUpdate->description = 'Частичное редактирование оплаты (возможность прикрепить акт с подписями)';
        $auth->add($paymentClientUpdate);

        $paymentDelete = $auth->createPermission('payment/delete');
        $paymentDelete->description = 'Удаление оплаты';
        $auth->add($paymentDelete);
        
        // priceList (услуги)
        $priceListSee = $auth->createPermission('price_list/see');
        $priceListSee->description = 'Просмотр услуг';
        $auth->add($priceListSee);

        $priceListCreate = $auth->createPermission('price_list/create');
        $priceListCreate->description = 'Создание услуги';
        $auth->add($priceListCreate);

        $priceListUpdate = $auth->createPermission('price_list/update');
        $priceListUpdate->description = 'Редактирование услуги';
        $auth->add($priceListUpdate);

        $priceListDelete = $auth->createPermission('price_list/delete');
        $priceListDelete->description = 'Удаление услуги';
        $auth->add($priceListDelete);
                
        // archivePriceList (архив услуг)
        $archivePriceListSee = $auth->createPermission('archive_price_list/see');
        $archivePriceListSee->description = 'Просмотр архива услуг';
        $auth->add($archivePriceListSee);

        // batch (партия)
        $batchSee = $auth->createPermission('batch/see');
        $batchSee->description = 'Просмотр партии';
        $auth->add($batchSee);

        $batchCreate = $auth->createPermission('batch/create');
        $batchCreate->description = 'Создание партии';
        $auth->add($batchCreate);

        $batchUpdate = $auth->createPermission('batch/update');
        $batchUpdate->description = 'Редактирование партии';
        $auth->add($batchUpdate);

        $batchDelete = $auth->createPermission('batch/delete');
        $batchDelete->description = 'Удаление партии';
        $auth->add($batchDelete);

        // sample (проба)
        $sampleSee = $auth->createPermission('sample/see');
        $sampleSee->description = 'Просмотр проб';
        $auth->add($sampleSee);

        $sampleCreate = $auth->createPermission('sample/create');
        $sampleCreate->description = 'Создание пробы';
        $auth->add($sampleCreate);

        $sampleUpdate = $auth->createPermission('sample/update');
        $sampleUpdate->description = 'Редактирование пробы';
        $auth->add($sampleUpdate);

        $sampleDelete = $auth->createPermission('sample/delete');
        $sampleDelete->description = 'Удаление пробы';
        $auth->add($sampleDelete);
        
        $sampleLost = $auth->createPermission('sample/lost');
        $sampleLost->description = 'Изменение статуса потери пробы';
        $auth->add($sampleLost);

        // service (исследования)
        $serviceSee = $auth->createPermission('service/see');
        $serviceSee->description = 'Просмотр исследований';
        $auth->add($serviceSee);

        $serviceCreate = $auth->createPermission('service/create');
        $serviceCreate->description = 'Создание исследования';
        $auth->add($serviceCreate);

        $serviceUpdate = $auth->createPermission('service/update');
        $serviceUpdate->description = 'Редактирование исследования';
        $auth->add($serviceUpdate);

        $serviceDelete = $auth->createPermission('service/delete');
        $serviceDelete->description = 'Удаление исследования';
        $auth->add($serviceDelete);

        // staff (сотрудники)
        $staffSee = $auth->createPermission('staff/see');
        $staffSee->description = 'Просмотр сотрудников';
        $auth->add($staffSee);

        $staffCreate = $auth->createPermission('staff/create');
        $staffCreate->description = 'Создание сотрудника';
        $auth->add($staffCreate);

        $staffUpdate = $auth->createPermission('staff/update');
        $staffUpdate->description = 'Редактирование сотрудника';
        $auth->add($staffUpdate);

        $staffDelete = $auth->createPermission('staff/delete');
        $staffDelete->description = 'Удаление сотрудника';
        $auth->add($staffDelete);

        // Насследование

        // registration (регистрация)
        $auth->addChild($registration, $organizationSee);
        $auth->addChild($registration, $organizationCreate);
        $auth->addChild($registration, $organizationUpdate);
        $auth->addChild($registration, $organizationDelete);

        $auth->addChild($registration, $batchSee);
        $auth->addChild($registration, $batchCreate);
        $auth->addChild($registration, $batchUpdate);
        $auth->addChild($registration, $batchDelete);

        $auth->addChild($registration, $sampleSee);
        $auth->addChild($registration, $sampleCreate);
        $auth->addChild($registration, $sampleUpdate);
        $auth->addChild($registration, $sampleDelete);
        $auth->addChild($registration, $sampleLost);
        
        $auth->addChild($registration, $contractSee);
        $auth->addChild($registration, $contractCreate);
        $auth->addChild($registration, $contractUpdate);
        $auth->addChild($registration, $contractDelete);

        // laboratory (лаборатория)
        $auth->addChild($laboratory, $serviceSee);
        $auth->addChild($laboratory, $serviceCreate);
        $auth->addChild($laboratory, $serviceUpdate);
        $auth->addChild($laboratory, $serviceDelete);

        $auth->addChild($laboratory, $sampleLost);

        // booker (Бухгалтер)
        $auth->addChild($booker, $paymentSee);
        $auth->addChild($booker, $paymentCreate);
        $auth->addChild($booker, $paymentUpdate);
        $auth->addChild($booker, $paymentDelete);

        $auth->addChild($booker, $priceListSee);
        $auth->addChild($booker, $priceListCreate);
        $auth->addChild($booker, $priceListUpdate);
        $auth->addChild($booker, $priceListDelete);

        $auth->addChild($booker, $archivePriceListSee);

        $auth->addChild($booker, $organizationSee);

        $auth->addChild($booker, $contractSee);

        // client (Заказчик)
        $auth->addChild($client, $priceListSee);

        $auth->addChild($client, $paymentClientUpdate);

        // admin (администратор)
        $auth->addChild($admin, $departamentSee);
        $auth->addChild($admin, $departamentCreate);
        $auth->addChild($admin, $departamentUpdate);
        $auth->addChild($admin, $departamentDelete);

        $auth->addChild($admin, $jobSee);
        $auth->addChild($admin, $jobCreate);
        $auth->addChild($admin, $jobUpdate);
        $auth->addChild($admin, $jobDelete);

        $auth->addChild($admin, $staffSee);
        $auth->addChild($admin, $staffCreate);
        $auth->addChild($admin, $staffUpdate);
        $auth->addChild($admin, $staffDelete);

        $auth->addChild($admin, $userSee);
        $auth->addChild($admin, $userCreate);
        $auth->addChild($admin, $userUpdate);
        $auth->addChild($admin, $userDelete);

        $auth->addChild($admin, $manageRoles);

        $auth->addChild($admin, $paymentClientUpdate);

        // Администратор наследует все права других ролей
        $auth->addChild($admin, $registration);
        $auth->addChild($admin, $booker);
        $auth->addChild($admin, $laboratory);

        // Назначение роли admin для пользователя ***************
        $user = User::findByUsername('***************');
        $auth->assign($admin, $user->id);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $this->delete($auth->itemChildTable);
        $this->delete($auth->itemTable);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240513_122843_rbac_roles cannot be reverted.\n";

        return false;
    }
    */
}
