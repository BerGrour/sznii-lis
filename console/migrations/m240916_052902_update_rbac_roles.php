<?php

use yii\db\Migration;

/**
 * Class m240916_052902_update_rbac_roles
 */
class m240916_052902_update_rbac_roles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $laboratory = $auth->getRole('laboratory');

        $priceListUpdate = $auth->getPermission('price_list/update');
        $archivePriceListSee = $auth->getPermission('archive_price_list/see');
        $batchSee = $auth->getPermission('batch/see');
        $sampleSee = $auth->getPermission('sample/see');

        $auth->addChild($laboratory, $priceListUpdate);
        $auth->addChild($laboratory, $archivePriceListSee);
        $auth->addChild($laboratory, $batchSee);
        $auth->addChild($laboratory, $sampleSee);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $laboratory = $auth->getRole('laboratory');

        $priceListUpdate = $auth->getPermission('price_list/update');
        $archivePriceListSee = $auth->getPermission('archive_price_list/see');
        $batchSee = $auth->getPermission('batch/see');
        $sampleSee = $auth->getPermission('sample/see');

        $auth->removeChild($laboratory, $priceListUpdate);
        $auth->removeChild($laboratory, $archivePriceListSee);
        $auth->removeChild($laboratory, $batchSee);
        $auth->removeChild($laboratory, $sampleSee);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240916_052902_update_rbac_roles cannot be reverted.\n";

        return false;
    }
    */
}
