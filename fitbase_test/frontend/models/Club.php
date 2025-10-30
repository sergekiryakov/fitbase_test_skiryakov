<?php

namespace frontend\models;

use common\models\User;
use common\models\StatusReference;
use common\models\traits\SoftDeleteTrait;
use common\models\queries\SoftDeleteQuery;
use Ramsey\Uuid\Uuid;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\models\interfaces\SoftDeleteInterface;

/**
 * This is the model class for table "club".
 *
 * @property string $id
 * @property string $name
 * @property string $address
 * @property int $status
 * @property string $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property string $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 *
 * @property User $author
 * @property User $editor
 * @property User $deleter
 */
class Club extends ActiveRecord implements SoftDeleteInterface
{
    use SoftDeleteTrait;

    public function beforeValidate(): bool
    {
        if ($this->isNewRecord && empty($this->id)) {
            $this->id = Uuid::uuid4()->toString();
        }

        if ($this->isNewRecord && ($this->status === null || $this->status === '')) {
            $srQuery = StatusReference::find()->where(['entity' => 'club']);

            $inactiveRow = (clone $srQuery)->andWhere(['code' => 'inactive'])->one();
            if ($inactiveRow) {
                $this->status = (int)$inactiveRow->id;
            } else {
                $firstRow = $srQuery->orderBy(['id' => SORT_ASC])->one();
                if ($firstRow) {
                    $this->status = (int)$firstRow->id;
                }
            }
        }

        return parent::beforeValidate();
    }

    public static function tableName(): string
    {
        return '{{%club}}';
    }

    /**
     * Use SoftDeleteQuery to allow ->active()/->deleted()/->notDeleted()
     *
     * @return SoftDeleteQuery
     */
    public static function find(): SoftDeleteQuery
    {
        return new SoftDeleteQuery(static::class);
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
        ];
    }

    public function rules()
    {
        return [
            [['name', 'address'], 'required'],
            [['address'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['id', 'created_by', 'updated_by', 'deleted_by'], 'string', 'max' => 36],
            [['name'], 'string', 'max' => 255],
            [['status'], 'integer'],
            [['id'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['deleted_by' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'address' => 'Address',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'deleted_by' => 'Deleted By',
        ];
    }

     /**
     * Gets author [[User]].
     *
     * @return ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets editor [[User]].
     *
     * @return ActiveQuery
     */
    public function getEditor()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Gets deleter [[User]].
     *
     * @return ActiveQuery
     */
    public function getDeleter()
    {
        return $this->hasOne(User::class, ['id' => 'deleted_by']);
    }
}
