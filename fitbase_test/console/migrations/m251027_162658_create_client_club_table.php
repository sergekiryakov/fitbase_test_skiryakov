<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client_club}}`.
 */
class m251027_162658_create_client_club_table extends Migration
{
   public function safeUp(): void
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%client_club}}', [
            'client_id' => $this->char(36)->notNull(),
            'club_id' => $this->char(36)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk_client_club', '{{%client_club}}', ['client_id', 'club_id']);

        $this->createIndex('idx_client_club_client', '{{%client_club}}', 'client_id');
        $this->createIndex('idx_client_club_club', '{{%client_club}}', 'club_id');

        $this->addForeignKey('fk_client_club_client', '{{%client_club}}', 'client_id', '{{%client}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_client_club_club', '{{%client_club}}', 'club_id', '{{%club}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_client_club_club', '{{%client_club}}');
        $this->dropForeignKey('fk_client_club_client', '{{%client_club}}');

        $this->dropIndex('idx_client_club_club', '{{%client_club}}');
        $this->dropIndex('idx_client_club_client', '{{%client_club}}');

        $this->dropTable('{{%client_club}}');
    }
}
