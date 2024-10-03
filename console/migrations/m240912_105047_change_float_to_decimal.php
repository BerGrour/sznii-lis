<?php

use yii\db\Migration;

/**
 * Class m240912_105047_change_float_to_decimal
 */
class m240912_105047_change_float_to_decimal extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('price_list', 'price', $this->decimal(10, 2));
        $this->alterColumn('archive_price_list', 'price', $this->decimal(10, 2));
        $this->alterColumn('service', 'price', $this->decimal(10, 2));
        $this->alterColumn('service', 'pre_sum', $this->decimal(10, 2));
        $this->alterColumn('payment', 'fact_sum', $this->decimal(10, 2));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('price_list', 'price', $this->float());
        $this->alterColumn('archive_price_list', 'price', $this->float());
        $this->alterColumn('service', 'price', $this->float());
        $this->alterColumn('service', 'pre_sum', $this->float());
        $this->alterColumn('payment', 'fact_sum', $this->float());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240912_105047_change_float_to_decimal cannot be reverted.\n";

        return false;
    }
    */
}
