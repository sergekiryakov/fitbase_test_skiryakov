<?php

namespace app\components;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * SoftDeleteBehavior
 *
 * Provides soft-delete and status logic for ActiveRecord.
 *
 * Configuration options:
 * - statusAttribute (string) default 'status'
 * - deletedAtAttribute (string) default 'deleted_at'
 * - deletedByAttribute (string) default 'deleted_by'
 * - deletedStatusValue (int) default 0
 * - inactiveStatusValue (int) default 5
 * - activeStatusValue (int) default 10
 * - replaceDelete (bool) default false — if true, behavior intercepts delete() and performs soft delete
 */
class SoftDeleteBehavior extends Behavior
{
    // Default status values (same scheme we used before)
    public const STATUS_DELETED   = 'deleted';
    public const STATUS_INACTIVE  = 'inactive';
    public const STATUS_ACTIVE    = 'active';

    public string $statusAttribute = 'status';
    public string $deletedAtAttribute = 'deleted_at';
    public string $deletedByAttribute = 'deleted_by';

    public int $deletedStatusValue = self::STATUS_DELETED;
    public int $inactiveStatusValue = self::STATUS_INACTIVE;
    public int $activeStatusValue = self::STATUS_ACTIVE;

    /**
     * If true, convert physical delete() calls into soft delete.
     * Note: when enabled, beforeDelete event will be handled and actual deletion prevented.
     */
    public bool $replaceDelete = false;

    public function events(): array
    {
        $events = [];
        if ($this->replaceDelete) {
            // Intercept beforeDelete to perform soft delete instead of actual deletion
            $events[ActiveRecord::EVENT_BEFORE_DELETE] = 'handleBeforeDeleteEvent';
        }
        return $events;
    }

    /**
     * Soft delete the owner record.
     *
     * @param string|null $userId optional user id who performs deletion. If null, Yii::$app->user->id is used.
     * @return bool whether saving succeeded
     */
    public function softDelete(?string $userId = null): bool
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;

        // set status
        $owner->{$this->statusAttribute} = $this->deletedStatusValue;

        // set deleted_at
        if ($this->deletedAtAttribute !== null) {
            $owner->{$this->deletedAtAttribute} = (new Expression('NOW()'));
        }

        // set deleted_by
        if ($this->deletedByAttribute !== null) {
            $owner->{$this->deletedByAttribute} = $userId ?? (Yii::$app->has('user') ? Yii::$app->user->id : null);
        }

        // Save only the relevant attributes without validation
        $attributes = array_filter([$this->statusAttribute, $this->deletedAtAttribute, $this->deletedByAttribute]);

        return $owner->save(false, $attributes);
    }

    /**
     * Restore a soft-deleted record (set status back to active or provided value).
     *
     * @param int|null $status optional status to set (default = active)
     * @return bool
     */
    public function restore(?int $status = null): bool
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        $owner->{$this->statusAttribute} = $status ?? $this->activeStatusValue;

        if ($this->deletedAtAttribute !== null) {
            $owner->{$this->deletedAtAttribute} = null;
        }
        if ($this->deletedByAttribute !== null) {
            $owner->{$this->deletedByAttribute} = null;
        }

        $attributes = array_filter([$this->statusAttribute, $this->deletedAtAttribute, $this->deletedByAttribute]);

        return $owner->save(false, $attributes);
    }

    /**
     * Check if owner is deleted.
     */
    public function isDeleted(): bool
    {
        return (int)$this->owner->{$this->statusAttribute} === $this->deletedStatusValue;
    }

    /**
     * Check if owner is active.
     */
    public function isActive(): bool
    {
        return (int)$this->owner->{$this->statusAttribute} === $this->activeStatusValue;
    }

    /**
     * Handler for beforeDelete event when replaceDelete = true.
     * We perform soft delete and cancel the physical delete.
     *
     * @param \yii\base\ModelEvent $event
     */
    public function handleBeforeDeleteEvent($event): void
    {
        // perform soft delete
        $this->softDelete();
        // cancel the actual deletion
        $event->isValid = false;
    }
}
