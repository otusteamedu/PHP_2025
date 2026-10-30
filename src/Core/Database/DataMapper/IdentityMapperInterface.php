<?php

declare(strict_types=1);

namespace App\Core\Database\DataMapper;

interface IdentityMapperInterface
{
    public function hydrateId(object $entity, int $id): void;
}
