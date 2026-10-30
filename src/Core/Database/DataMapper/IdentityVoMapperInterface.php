<?php

declare(strict_types=1);

namespace App\Core\Database\DataMapper;

interface IdentityVoMapperInterface
{
    public function createIdentity(int $id): object;
}
