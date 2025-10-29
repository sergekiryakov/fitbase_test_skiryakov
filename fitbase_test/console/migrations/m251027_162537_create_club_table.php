<?php

use yii\db\Migration;
use common\models\StatusReference;
/**
 * Handles the creation of table `{{%club}}`.
 */
class m251027_162537_create_club_table extends Migration
{
     public function safeUp(): void
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%club}}', [
            'id' => $this->char(36)->notNull(),
            'name' => $this->string(255)->notNull(),
            'address' => $this->text()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(StatusReference::STATUS_DEFAULT),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->null(),
            'deleted_at' => $this->dateTime()->null(),
            'created_by' => $this->char(36)->notNull(),
            'updated_by' => $this->char(36)->null(),
            'deleted_by' => $this->char(36)->null(),
        ], $tableOptions);

        $this->addPrimaryKey('pk_club_id', '{{%club}}', 'id');

        $this->createIndex('idx_club_status', '{{%club}}', 'status');
        $this->createIndex('idx_club_created_by', '{{%club}}', 'created_by');
        $this->createIndex('idx_club_updated_by', '{{%club}}', 'updated_by');
        $this->createIndex('idx_club_deleted_by', '{{%club}}', 'deleted_by');

        $this->addForeignKey('fk_club_status_reference', '{{%club}}', 'status', '{{%status_reference}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk_club_created_by_user', '{{%club}}', 'created_by', '{{%user}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk_club_updated_by_user', '{{%club}}', 'updated_by', '{{%user}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_club_deleted_by_user', '{{%club}}', 'deleted_by', '{{%user}}', 'id', 'SET NULL', 'CASCADE');

        if ($this->db->driverName === 'mysql') {
            $this->execute("ALTER TABLE {{%club}} ADD FULLTEXT INDEX ft_club_name (name)");
        }
    }

    public function safeDown(): void
    {
        if ($this->db->driverName === 'mysql') {
            $this->execute("ALTER TABLE {{%club}} DROP INDEX ft_club_name");
        }

        $this->dropForeignKey('fk_club_deleted_by_user', '{{%club}}');
        $this->dropForeignKey('fk_club_updated_by_user', '{{%club}}');
        $this->dropForeignKey('fk_club_created_by_user', '{{%club}}');
        $this->dropForeignKey('fk_club_status_reference', '{{%club}}');

        $this->dropIndex('idx_club_deleted_by', '{{%club}}');
        $this->dropIndex('idx_club_updated_by', '{{%club}}');
        $this->dropIndex('idx_club_created_by', '{{%club}}');
        $this->dropIndex('idx_club_status', '{{%club}}');

        $this->dropTable('{{%club}}');
    }
}
