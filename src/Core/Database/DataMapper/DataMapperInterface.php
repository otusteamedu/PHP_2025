<?php

declare(strict_types=1);

namespace App\Core\Database\DataMapper;

use App\Domain\Shared\Entity\EntityInterface;

interface DataMapperInterface
{
    public function mapRowToEntity(array $row): EntityInterface;

    public function mapEntityToRow(EntityInterface $entity): array;

    public function hydrateId(EntityInterface $entity, int $id): void;
}
