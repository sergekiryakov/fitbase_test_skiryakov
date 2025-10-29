<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\traits\SoftDeleteTrait;
use yii\web\IdentityInterface;
use Ramsey\Uuid\Uuid;

/**
 * User model
 *
 * @property string $id UUID (char(36))
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string|null $verification_token
 * @property string $email
 * @property int $status
 * @property string $created_at 
 * @property string|null $updated_at
 *
 * @property StatusReference $statusReference
 */
class User extends ActiveRecord implements IdentityInterface
{
    use SoftDeleteTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    /**
     * Generate UUID for new records and assign default status for entity='user' when needed.
     *
     * @return bool
     */
    public function beforeValidate(): bool
    {
        if ($this->isNewRecord && empty($this->id)) {
            $this->id = Uuid::uuid4()->toString();
        }

        return parent::beforeValidate();
    }

    /**
     * {@inheritdoc}
     */
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['username', 'auth_key', 'password_hash', 'email'], 'required'],
            [['username', 'email'], 'unique'],

            ['status', 'integer'],
            ['status', 'default', 'value' => Yii::$app->statusManager->getIdByCode('inactive')],
            ['status', 'exist', 'targetClass' => StatusReference::class, 'targetAttribute' => 'id', 'filter' => ['entity' => null]],

            [['id'], 'string', 'max' => 36],
            [['id'], 'filter', 'filter' => 'trim'],

            [['username', 'auth_key', 'password_hash'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['password_reset_token', 'verification_token'], 'string', 'max' => 255],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * Relation to StatusReference row.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatusReference()
    {
        return $this->hasOne(StatusReference::class, ['id' => 'status']);
    }

    /**
     * Returns map id => label for dropdowns / filters for entity='user'.
     *
     * @return array
     */
    public static function statusList(): array
    {
        $rows = StatusReference::find()->where(['entity' => 'user'])->orderBy(['id' => SORT_ASC])->all();
        $map = [];
        foreach ($rows as $r) {
            $map[(int)$r->id] = (string)$r->label;
        }
        return $map;
    }

    /**
     * Returns human-readable status label.
     *
     * @return string
     */
    public function getStatusName(): string
    {
        return $this->statusReference ? (string)$this->statusReference->label : Yii::t('app', 'Unknown');
    }

    /**
     * Returns status code from status reference (e.g. 'active', 'inactive').
     *
     * @return string|null
     */
    public function getStatusCode(): ?string
    {
        return $this->statusReference ? (string)$this->statusReference->code : null;
    }

    /**
     * Check whether user is active (status.code === 'active').
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->getStatusCode() === 'active';
    }

    /**
     * Set status by status reference code (convenience).
     *
     * @param string $code
     * @return bool true if set successfully
     */
    public function setStatusByCode(string $code): bool
    {
        $row = StatusReference::find()->where(['entity' => 'user', 'code' => $code])->one();
        if ($row) {
            $this->status = (int)$row->id;
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * Returns user only if status.code = 'active'
     *
     * @param string|int $id
     * @return static|null
     */
    public static function findIdentity($id)
    {
        return static::findOne([
            'id' => (string)$id,
            'status' => Yii::$app->statusManager->getIdByCode('active', null)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Find user by username (only active users).
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername(string $username)
    {
        return static::findOne([
            'username' => $username,
            'status' => Yii::$app->statusManager->getIdByCode('active', null)
        ]);
    }

    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'password_reset_token' => $token,
            'status' => Yii::$app->statusManager->getIdByCode('active', null),
        ]);
    }
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public static function findByVerificationToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => Yii::$app->statusManager->getIdByCode('inactive')
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @return string UUID
     */
    public function getId(): string
    {
        return (string)$this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(): ?string
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password.
     *
     * @param string $password
     * @return bool
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Sets password hash from plain password.
     *
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" auth key.
     *
     * @return void
     */
    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token.
     *
     * @return void
     */
    public function generatePasswordResetToken(): void
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new email verification token.
     *
     * @return void
     */
    public function generateEmailVerificationToken(): void
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token.
     *
     * @return void
     */
    public function removePasswordResetToken(): void
    {
        $this->password_reset_token = null;
    }
}
