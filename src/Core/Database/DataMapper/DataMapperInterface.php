<?php

declare(strict_types=1);

namespace App\Core\Database\DataMapper;

interface DataMapperInterface
{
    public function mapRowToEntity(array $row): object;

    public function mapEntityToRow(object $entity): array;
}
