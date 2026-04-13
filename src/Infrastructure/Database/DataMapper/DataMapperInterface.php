<?php

namespace App\Infrastructure\Database\DataMapper;

use App\Domain\Entity\EntityInterface;

interface DataMapperInterface
{
    public function mapRowToEntity(array $row): EntityInterface;

    public function mapEntityToRow(EntityInterface $entity): array;

    public function hydrateId(EntityInterface $entity, int $id): void;
}
