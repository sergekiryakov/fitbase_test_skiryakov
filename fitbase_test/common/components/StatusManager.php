<?php

namespace common\components;

use Yii;
use yii\base\Component;
use common\models\StatusReference;
use InvalidArgumentException;

class StatusManager extends Component
{
    /** @var StatusReference[] indexed by id */
    private array $_byId = [];

    /** @var StatusReference[] indexed by "entity:code" (entity may be empty string for global) */
    private array $_byCode = [];

    private bool $loaded = false;

    public function init(): void
    {
        parent::init();
        $this->loadAll();
    }

    protected function loadAll(): void
    {
        if ($this->loaded) {
            return;
        }
        $rows = StatusReference::find()->all();
        foreach ($rows as $r) {
            $this->_byId[(int)$r->id] = $r;
            $entityKey = (string)($r->entity ?? '');
            $key = $this->makeCodeKey($entityKey, $r->code);
            $this->_byCode[$key] = $r;
        }

        $this->loaded = true;
    }

    private function makeCodeKey(string $entity, string $code): string
    {
        return $entity . ':' . $code;
    }

    public function getById(int $id): ?StatusReference
    {
        return $this->_byId[$id] ?? null;
    }

    /**
     * Get StatusReference by code and optional entity.
     *
     * @param string $code
     * @param string|null $entity
     * @return StatusReference|null
     */
    public function getByCode(string $code, ?string $entity = null): ?StatusReference
    {
        $entityKey = (string)($entity ?? '');
        $key = $this->makeCodeKey($entityKey, $code);
        if (isset($this->_byCode[$key])) {
            return $this->_byCode[$key];
        }
        $globalKey = $this->makeCodeKey('', $code);
        return $this->_byCode[$globalKey] ?? null;
    }

    /**
     * Get ID by code (optionally for specific entity)
     *
     * @param string $code
     * @param string|null $entity
     * @return int
     */
    public function getIdByCode(string $code, ?string $entity = null): int
    {
        $r = $this->getByCode($code, $entity);
        if (empty($r)) {
            throw new InvalidArgumentException("Invalid status code for entity {$entity}");
        }

        return (int)$r->id;
    }

    /**
     * Return array of StatusReference for given entity or global (entity NULL).
     *
     * @param string|null $entity
     * @return StatusReference[]
     */
    public function allForEntity(?string $entity = null): array
    {
        $res = [];
        foreach ($this->_byId as $reference) {
            if (($reference->entity === null || $reference->entity === '') || $reference->entity === $entity) {
                if ($entity === null || $reference->entity === null || $reference->entity === '' || $reference->entity === $entity) {
                    $res[] = $reference;
                }
            }
        }
        return $res;
    }


    /**
     * getLabelFor
     *
     * @param  mixed $modelOrEntity
     * @param  mixed $statusId
     * @return string
     */
    public function getLabelFor(object|string $modelOrEntity, int $statusId): string
    {
        $entity = is_object($modelOrEntity)
            ? (new \ReflectionClass($modelOrEntity))->getShortName()
            : (string)$modelOrEntity;

        $status = $this->getById($statusId);

        if (!$status) {
            return Yii::t('app', 'Unknown');
        }

        if ($status->entity !== null && $status->entity !== '' && strcasecmp($status->entity, $entity) !== 0) {
            return Yii::t('app', 'Unknown');
        }

        return (string)($status->label ?? $status->code ?? $statusId);
    }
}
