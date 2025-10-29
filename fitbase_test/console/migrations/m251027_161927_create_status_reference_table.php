<?php

use yii\db\Migration;
use common\models\StatusReference;
/**
 * Handles the creation of table `{{%status_reference}}`.
 */
class m251027_161927_create_status_reference_table extends Migration
{
    public function safeUp(): void
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%status_reference}}', [
            'id' => $this->smallInteger()->notNull(),
            'code' => $this->string(50)->notNull()->unique(),
            'label' => $this->string(100)->notNull(),
            'entity' => $this->string(100)->null(),
            'is_default' => $this->boolean()->notNull()->defaultValue(false),
            'sort' => $this->integer()->notNull()->defaultValue(100),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->addPrimaryKey('pk_status_reference', '{{%status_reference}}', 'id');

        // seed base statuses (IDs chosen to be compatible with earlier scheme)
        $now = date('Y-m-d H:i:s');
        $this->batchInsert('{{%status_reference}}',
            ['id','code','label','entity','is_default','sort','created_at'],
            [
                [StatusReference::STATUS_ACTIVE, 'active', 'Active', null, true, 10, $now],
                [StatusReference::STATUS_INACTIVE,  'inactive', 'Inactive', null, false, 20, $now],
                [StatusReference::STATUS_DELETED,  'deleted', 'Deleted', null, false, 30, $now],
            ]
        );
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%status_reference}}');
    }
}
