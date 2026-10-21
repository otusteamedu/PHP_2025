<?php

declare(strict_types=1);

namespace App\Core\Database\DataMapper;

use App\Core\Database\Entity\EntityInterface;

interface IdentityMapperInterface
{
    public function hydrateId(EntityInterface $entity, int $id): void;
}
