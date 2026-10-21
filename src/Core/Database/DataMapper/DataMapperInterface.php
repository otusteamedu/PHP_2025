<?php

declare(strict_types=1);

namespace App\Core\Database\DataMapper;

use App\Core\Database\Entity\EntityInterface;

interface DataMapperInterface
{
    public function mapRowToEntity(array $row): EntityInterface;

    public function mapEntityToRow(EntityInterface $entity): array;
}
