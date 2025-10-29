<?php
namespace common\models\interfaces;

interface SoftDeleteInterface
{
    public function softDelete(?string $userId = null): bool;
    public function restore(?int $statusId = null): bool;
    public function isDeleted(): bool;
    public function getStatusId(): ?int;
    public function setStatusId(int $id): void;
}
