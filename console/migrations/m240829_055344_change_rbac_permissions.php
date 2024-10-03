<?php

use yii\db\Migration;

/**
 * Class m240829_055344_change_rbac_permissions
 */
class m240829_055344_change_rbac_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $registration = $auth->getRole('registration');
        $laboratory = $auth->getRole('laboratory');

        $priceListSee = $auth->getPermission('price_list/see');
        $paymentSee = $auth->getPermission('payment/see');
        $paymentCreate = $auth->getPermission('payment/create');
        $paymentUpdate = $auth->getPermission('payment/update');

        $auth->addChild($laboratory, $priceListSee);
        $auth->addChild($registration, $paymentSee);
        $auth->addChild($registration, $paymentCreate);
        $auth->addChild($registration, $paymentUpdate);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $registration = $auth->getRole('registration');
        $laboratory = $auth->getRole('laboratory');

        $priceListSee = $auth->getPermission('price_list/see');
        $paymentSee = $auth->getPermission('payment/see');
        $paymentCreate = $auth->getPermission('payment/create');
        $paymentUpdate = $auth->getPermission('payment/update');

        $auth->removeChild($laboratory, $priceListSee);
        $auth->removeChild($registration, $paymentSee);
        $auth->removeChild($registration, $paymentCreate);
        $auth->removeChild($registration, $paymentUpdate);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240829_055344_change_rbac_permissions cannot be reverted.\n";

        return false;
    }
    */
}
