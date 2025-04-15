<?php

declare(strict_types=1);

namespace App\Domain\Service;


use Ramsey\Uuid\Uuid;

class UuidService
{
    public static function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}