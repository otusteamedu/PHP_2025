<?php

declare(strict_types=1);

namespace App\Core\Storage\KeyValue\Memcached;

use App\Core\Storage\KeyValue\Shared\DependencyCacheInterface;

class MemcachedDependencyCacheAdapter implements DependencyCacheInterface
{
    public function __construct(
        private readonly \Memcached $memcached,
    ) {
    }

    public function get(string $key): mixed
    {
        $value = $this->memcached->get($key);

        if ($value === false) {
            return null;
        }

        return unserialize($value);
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        $serialized = serialize($value);

        $expiration = $ttl ?? 0;

        return $this->memcached->set($key, $serialized, $expiration);
    }

    public function clear(): bool
    {
        if (!$this->memcached->flush()) {
            throw new \RuntimeException('Failed to flush Memcached cache.');
        }

        return true;
    }
}