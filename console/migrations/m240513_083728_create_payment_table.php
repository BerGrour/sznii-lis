<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%payment}}`.
 */
class m240513_083728_create_payment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%payment}}', [
            'id' => $this->primaryKey(),
            'act_num' => $this->integer()->notNull(),
            'act_date' => $this->date(),
            'list_date' => $this->date(),
            'return_date' => $this->date(),
            'fact_sum' => $this->float(),
            'pay_date' => $this->date(),
            'file_act' => $this->integer(11),
            'file_act_client' => $this->integer(11),
            'file_pay' => $this->integer(11),
            'file_invoice' => $this->integer(11),
            'locked' => $this->smallInteger(6)->notNull()->defaultValue(0)
        ]);

        $this->createIndex('idx-payment-file_act', 'payment', 'file_act');
        $this->addForeignKey('fk-payment-file_act', 'payment', 'file_act', 'file', 'id', 'SET NULL');

        $this->createIndex('idx-payment-file_act_client', 'payment', 'file_act_client');
        $this->addForeignKey('fk-payment-file_act_client', 'payment', 'file_act_client', 'file', 'id', 'SET NULL');

        $this->createIndex('idx-payment-file_pay', 'payment', 'file_pay');
        $this->addForeignKey('fk-payment-file_pay', 'payment', 'file_pay', 'file', 'id', 'SET NULL');

        $this->createIndex('idx-payment-file_invoice', 'payment', 'file_invoice');
        $this->addForeignKey('fk-payment-file_invoice', 'payment', 'file_invoice', 'file', 'id', 'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-payment-file_act','payment');
        $this->dropIndex('idx-payment-file_act','payment');

        $this->dropForeignKey('fk-payment-file_act_client','payment');
        $this->dropIndex('idx-payment-file_act_client','payment');

        $this->dropForeignKey('fk-payment-file_pay','payment');
        $this->dropIndex('idx-payment-file_pay','payment');

        $this->dropForeignKey('fk-payment-file_invoice','payment');
        $this->dropIndex('idx-payment-file_invoice','payment');

        $this->dropTable('{{%payment}}');
    }
}
