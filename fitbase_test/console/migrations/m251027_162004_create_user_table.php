<?php

use yii\db\Migration;
use common\models\StatusReference;
/**
 * Handles the creation of table `{{%user}}`.
 */
class m251027_162004_create_user_table extends Migration
{
   public function safeUp(): void
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->char(36)->notNull(),
            'username' => $this->string(255)->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'verification_token' => $this->string()->defaultValue(null),
            'email' => $this->string(255)->notNull()->unique(),
            'status' => $this->smallInteger()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->null(),
            'deleted_at' => $this->dateTime()->null(),
            'deleted_by' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->addPrimaryKey('pk_user_id', '{{%user}}', 'id');
        $this->createIndex('idx_user_status', '{{%user}}', 'status');
        $this->addForeignKey('fk_user_status_reference', '{{%user}}', 'status', '{{%status_reference}}', 'id', 'RESTRICT', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_user_status_reference', '{{%user}}');
        $this->dropIndex('idx_user_status', '{{%user}}');
        $this->dropTable('{{%user}}');
    }
}
