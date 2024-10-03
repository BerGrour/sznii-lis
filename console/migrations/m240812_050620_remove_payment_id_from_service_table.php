<?php

use yii\db\Migration;

/**
 * Class m240812_050620_remove_payment_id_from_service_table
 */
class m240812_050620_remove_payment_id_from_service_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-service-payment_id','service');
        $this->dropIndex('idx-service-payment_id','service');

        $this->dropColumn("{{%service}}", "payment_id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn("{{%service}}", "payment_id", $this->integer(11)->after("completed_at"));
        
        $this->createIndex('idx-service-payment_id', 'service', 'payment_id');
        $this->addForeignKey('fk-service-payment_id', 'service', 'payment_id', 'payment', 'id', 'RESTRICT');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240812_050620_remove_payment_id_from_service_table cannot be reverted.\n";

        return false;
    }
    */
}
