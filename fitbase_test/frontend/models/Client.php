<?php

namespace frontend\models;

use Yii;
use common\models\User;
use common\models\traits\SoftDeleteTrait;
use common\models\queries\SoftDeleteQuery;
use Ramsey\Uuid\Uuid;
use common\models\StatusReference;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\models\interfaces\SoftDeleteInterface;

/**
 * This is the model class for table "client".
 *
 * @property string $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $middle_name
 * @property int $status
 * @property string $gender
 * @property string $birth_date
 * @property string $created_at
 * @property string $created_by
 * @property string|null $updated_at
 * @property string|null $updated_by
 * @property string|null $deleted_at
 * @property string|null $deleted_by
 *
 * @property ClientClub[] $clientClubs
 * @property Club[] $clubs
 * @property User $createdBy
 * @property User $deletedBy
 * @property StatusReference $status
 * @property User $updatedBy
 */
class Client extends \yii\db\ActiveRecord implements SoftDeleteInterface
{
    use SoftDeleteTrait;

    public $clubIds = [];

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

    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    public function beforeValidate(): bool
    {
        if ($this->isNewRecord && empty($this->id)) {
            $this->id = Uuid::uuid4()->toString();
        }

        if ($this->isNewRecord && ($this->status === null || $this->status === '')) {
            $srQuery = StatusReference::find()->where(['entity' => 'client']);

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

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => Yii::$app->statusManager->getIdByCode('inactive')],
            [['id', 'first_name', 'last_name', 'gender', 'birth_date', 'status'], 'required'],
            [['status'], 'integer'],
            [['gender'], 'string'],
            [['birth_date', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['id', 'created_by', 'updated_by', 'deleted_by'], 'string', 'max' => 36],
            [['first_name', 'last_name', 'middle_name'], 'string', 'max' => 255],
            ['gender', 'in', 'range' => array_keys(self::optsGender())],
            [['id'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['deleted_by' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => StatusReference::class, 'targetAttribute' => ['status' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['clubIds'], 'safe']
        ];
    }

    public function afterFind(): void
    {
        parent::afterFind();
        $this->clubIds = $this->getClubs()->select('id')->column();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'middle_name' => 'Middle Name',
            'status' => 'Status',
            'gender' => 'Gender',
            'birth_date' => 'Birth Date',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'deleted_at' => 'Deleted At',
            'deleted_by' => 'Deleted By',
        ];
    }

    /**
     *
     * @return SoftDeleteQuery
     */
    public static function find(): SoftDeleteQuery
    {
        return new SoftDeleteQuery(static::class);
    }

    /**
     * Gets query for [[ClientClubs]].
     *
     * @return \yii\db\ActiveQuery|\frontend\models\queries\ClientClubQuery
     */
    public function getClientClubs()
    {
        return $this->hasMany(ClientClub::class, ['client_id' => 'id'])->inverseOf('client');
    }

    /**
     * Gets query for [[Clubs]].
     *
     * @return \yii\db\ActiveQuery|\frontend\models\queries\ClubQuery
     */
    public function getClubs()
    {
        return $this->hasMany(Club::class, ['id' => 'club_id'])->via('clientClubs');
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery|\frontend\models\queries\StatusReferenceQuery
     */
    public function getStatus()
    {
        return $this->hasOne(StatusReference::class, ['id' => 'status'])->inverseOf('clients');
    }

    /**
     * column gender ENUM value labels
     * @return string[]
     */
    public static function optsGender()
    {
        return [
            self::GENDER_MALE => 'male',
            self::GENDER_FEMALE => 'female',
        ];
    }

    /**
     * @return string
     */
    public function displayGender()
    {
        return self::optsGender()[$this->gender];
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
