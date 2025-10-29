<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client}}`.
 */
class m251027_162601_create_client_table extends Migration
{
    public function safeUp(): void
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%client}}', [
            'id' => $this->char(36)->notNull(),
            'first_name' => $this->string(255)->notNull(),
            'last_name' => $this->string(255)->notNull(),
            'middle_name' => $this->string(255)->null(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'gender' => "ENUM('male','female') NOT NULL",
            'birth_date' => $this->date()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'created_by' => $this->char(36)->notNull(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->char(36)->null(),
            'deleted_at' => $this->dateTime()->null(),
            'deleted_by' => $this->char(36)->null(),
        ], $tableOptions);

        $this->addPrimaryKey('pk_client_id', '{{%client}}', 'id');

        $this->createIndex('idx_client_status', '{{%client}}', 'status');
        $this->createIndex('idx_client_created_by', '{{%client}}', 'created_by');
        $this->createIndex('idx_client_updated_by', '{{%client}}', 'updated_by');
        $this->createIndex('idx_client_deleted_by', '{{%client}}', 'deleted_by');
        $this->createIndex('idx_client_last_first', '{{%client}}', ['last_name', 'first_name']);

        $this->addForeignKey('fk_client_status_reference', '{{%client}}', 'status', '{{%status_reference}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk_client_created_by_user', '{{%client}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk_client_updated_by_user', '{{%client}}', 'updated_by', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_client_deleted_by_user', '{{%client}}', 'deleted_by', '{{%user}}', 'id', 'SET NULL', 'CASCADE');

        if ($this->db->driverName === 'mysql') {
            $this->execute("ALTER TABLE {{%client}} ADD FULLTEXT INDEX ft_client_fullname (first_name, last_name, middle_name)");
        }
    }

    public function safeDown(): void
    {
        if ($this->db->driverName === 'mysql') {
            $this->execute("ALTER TABLE {{%client}} DROP INDEX ft_client_fullname");
        }

        $this->dropForeignKey('fk_client_deleted_by_user', '{{%client}}');
        $this->dropForeignKey('fk_client_updated_by_user', '{{%client}}');
        $this->dropForeignKey('fk_client_created_by_user', '{{%client}}');
        $this->dropForeignKey('fk_client_status_reference', '{{%client}}');

        $this->dropIndex('idx_client_last_first', '{{%client}}');
        $this->dropIndex('idx_client_deleted_by', '{{%client}}');
        $this->dropIndex('idx_client_updated_by', '{{%client}}');
        $this->dropIndex('idx_client_created_by', '{{%client}}');
        $this->dropIndex('idx_client_status', '{{%client}}');

        $this->dropTable('{{%client}}');
    }
}
