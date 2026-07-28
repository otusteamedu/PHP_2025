<?php

declare(strict_types=1);

namespace App\Core\Storage\KeyValue\Redis;

use App\Core\Storage\KeyValue\Shared\DependencyCacheInterface;

class RedisDependencyCacheAdapter implements DependencyCacheInterface
{
    public function __construct(
        private readonly \Redis $redis,
    ) {
    }

    public function get(string $key): mixed
    {
        $value = $this->redis->get($key);

        if ($value === false) {
            return null;
        }

        return unserialize($value);
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        $serialized = serialize($value);

        if ($ttl !== null && $ttl > 0) {
            return $this->redis->setex($key, $ttl, $serialized);
        }

        return $this->redis->set($key, $serialized);
    }

    public function clear(): bool
    {
        $iterator = null;
        $keysToDelete = [];
        $pattern = self::KEY_PREFIX . '*';

        do {
            $keys = $this->redis->scan($iterator, $pattern, 100);
            if ($keys === false) {
                throw new \RuntimeException('Redis SCAN failed: connection error or server unreachable.');
            }
            foreach ($keys as $key) {
                $keysToDelete[] = $key;
            }
        } while ($iterator !== 0);

        if (empty($keysToDelete)) {
            return true;
        }

        $deletedCount = $this->redis->del($keysToDelete);

        if ($deletedCount === false) {
            throw new \RuntimeException('Failed to delete cache keys from Redis.');
        }

        return true;
    }
}
