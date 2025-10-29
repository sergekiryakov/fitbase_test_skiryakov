<?php
namespace common\models\traits;

use Yii;

/**
 * Trait to provide soft-delete functionality using status_reference (status).
 *
 * Model using this trait MUST have columns:
 * - status (smallint)
 * - deleted_at (datetime nullable)
 * - deleted_by (char(36) nullable)
 *
 * You can override attribute names by setting public properties on model:
 * public $statusAttribute = 'status';
 * public $deletedAtAttribute = 'deleted_at';
 * public $deletedByAttribute = 'deleted_by';
 */
trait SoftDeleteTrait
{
    public string $statusAttribute = 'status';
    public string $deletedAtAttribute = 'deleted_at';
    public string $deletedByAttribute = 'deleted_by';
    
    /**
     * softDelete
     *
     * @param  mixed $userId
     * @return bool
     */
    public function softDelete(?string $userId = null): bool
    {
        $sm = Yii::$app->statusManager;
        $deletedId = $sm->getIdByCode('deleted');

        $this->{$this->statusAttribute} = $deletedId;
        if ($this->deletedAtAttribute) {
            $this->{$this->deletedAtAttribute} = date('Y-m-d H:i:s');
        }
        if ($this->deletedByAttribute) {
            $this->{$this->deletedByAttribute} = $userId ?? (Yii::$app->has('user') ? Yii::$app->user->id : null);
        }

        return $this->save(false, array_filter([$this->statusAttribute, $this->deletedAtAttribute, $this->deletedByAttribute]));
    }
    
    /**
     * restore
     *
     * @param  mixed $statusId
     * @return bool
     */
    public function restore(?int $statusId = null): bool
    {
        $sm = Yii::$app->statusManager;
        $statusId = $statusId ?? $sm->getIdByCode('active');

        $this->{$this->statusAttribute} = $statusId;
        if ($this->deletedAtAttribute) {
            $this->{$this->deletedAtAttribute} = null;
        }
        if ($this->deletedByAttribute) {
            $this->{$this->deletedByAttribute} = null;
        }

        return $this->save(false, array_filter([$this->statusAttribute, $this->deletedAtAttribute, $this->deletedByAttribute]));
    }
    
    /**
     * isDeleted
     *
     * @return bool
     */
    public function isDeleted(): bool
    {
        $sm = Yii::$app->statusManager;
        $deletedId = $sm->getIdByCode('deleted');
        return ((int)$this->{$this->statusAttribute} === (int)$deletedId);
    }
    
    /**
     * getStatusId
     *
     * @return int
     */
    public function getStatusId(): ?int
    {
        return isset($this->{$this->statusAttribute}) ? (int)$this->{$this->statusAttribute} : null;
    }
    
    /**
     * setStatusId
     *
     * @param  mixed $id
     * @return void
     */
    public function setStatusId(int $id): void
    {
        $this->{$this->statusAttribute} = $id;
    }
}
