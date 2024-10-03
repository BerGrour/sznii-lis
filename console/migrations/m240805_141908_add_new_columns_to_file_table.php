<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%file}}`.
 */
class m240805_141908_add_new_columns_to_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%file}}', 'organization_id', $this->integer(11));
        $this->addColumn('{{%file}}', 'departament_id', $this->integer(11));
        
        $this->createIndex('idx-file-organization_id', 'file', 'organization_id');
        $this->addForeignKey('fk-file-organization_id', 'file', 'organization_id', 'organization', 'id', 'RESTRICT');

        $this->createIndex('idx-file-departament_id', 'file', 'departament_id');
        $this->addForeignKey('fk-file-departament_id', 'file', 'departament_id', 'departament', 'id', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-file-organization_id', 'file');
        $this->dropIndex('idx-file-organization_id', 'file');

        $this->dropForeignKey('fk-file-departament_id', 'file');
        $this->dropIndex('idx-file-departament_id', 'file');

        $this->dropColumn('{{%file}}', 'organization_id');
    }
}
