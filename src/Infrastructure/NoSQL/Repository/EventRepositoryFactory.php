<?php

declare(strict_types=1);

namespace App\Infrastructure\NoSQL\Repository;

use App\Application\DotEnvLoader;
use App\Infrastructure\NoSQL\Enum\EventStorage;
use App\Infrastructure\NoSQL\Memcached\MemcachedEventRepository;
use App\Infrastructure\NoSQL\Redis\RedisEventRepository;

class EventRepositoryFactory
{
    public static function create(): EventRepositoryInterface
    {
        $dotEnvLoader = new DotEnvLoader();
        $eventStorage = EventStorage::tryFrom($dotEnvLoader->getEnv('EVENT_STORAGE') ?? '');

        switch ($eventStorage) {
            case EventStorage::Memcached:
                $memcachedHost = $dotEnvLoader->getEnv('MEMCACHED_HOST');
                $memcachedPort = $dotEnvLoader->getEnv('MEMCACHED_PORT');
                return new MemcachedEventRepository($memcachedHost, (int) $memcachedPort);
            default:
                $redisHost = $dotEnvLoader->getEnv('REDIS_HOST');
                return new RedisEventRepository($redisHost);
        }
    }
}
