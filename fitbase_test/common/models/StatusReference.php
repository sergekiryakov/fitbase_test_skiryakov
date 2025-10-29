<?php

namespace common\models;

use yii\db\ActiveRecord;

class StatusReference extends ActiveRecord
{
    public const STATUS_ACTIVE = 10;
    public const STATUS_INACTIVE = 5;
    public const STATUS_DELETED = 0;

    public const STATUS_DEFAULT = self::STATUS_INACTIVE;

    public static function tableName(): string
    {
        return '{{%status_reference}}';
    }

    public static function primaryKey(): array
    {
        return ['id'];
    }

    public function rules(): array
    {
        return [
            [['id', 'code', 'label', 'created_at'], 'required'],
            [['id'], 'integer'],
            [['code'], 'string', 'max' => 50],
            [['label'], 'string', 'max' => 100],
            [['entity'], 'string', 'max' => 100],
        ];
    }
}
