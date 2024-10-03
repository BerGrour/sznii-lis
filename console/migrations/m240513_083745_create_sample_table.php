<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sample}}`.
 */
class m240513_083745_create_sample_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sample}}', [
            'id' => $this->primaryKey(),
            'identificator' => $this->string(100)->notNull()->unique(),
            'num' => $this->integer(11)->notNull(),
            'departament_id' => $this->integer(11)->notNull(),
            'batch_id' => $this->integer(11)->notNull(),
            'busy' => $this->integer(6)->notNull()->defaultValue(0),
            'losted_at' => $this->dateTime()
        ]);
        $this->createIndex('idx-sample-departament_id', 'sample', 'departament_id');
        $this->addForeignKey('fk-sample-departament_id', 'sample', 'departament_id', 'departament', 'id', 'RESTRICT');
        
        $this->createIndex('idx-sample-batch_id', 'sample', 'batch_id');
        $this->addForeignKey('fk-sample-batch_id', 'sample', 'batch_id', 'batch', 'id', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-sample-batch_id','sample');
        $this->dropIndex('idx-sample-batch_id','sample');

        $this->dropForeignKey('fk-sample-departament_id','sample');
        $this->dropIndex('idx-sample-departament_id','sample');
    
        $this->dropTable('{{%sample}}');
    }
}
