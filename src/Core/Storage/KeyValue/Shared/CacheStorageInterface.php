<?php

declare(strict_types=1);

namespace App\Core\Storage\KeyValue\Shared;

interface CacheStorageInterface
{
    public function get(string $key): mixed;

    public function set(string $key, mixed $value, ?int $ttl = null): bool;
}
