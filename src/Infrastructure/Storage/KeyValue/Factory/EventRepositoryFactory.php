<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage\KeyValue\Factory;

use App\Core\Config\DotEnvLoader;
use App\Domain\EventSystem\Interface\EventRepositoryInterface;
use App\Infrastructure\Storage\KeyValue\Repository\MemcachedEventRepository;
use App\Infrastructure\Storage\KeyValue\Repository\RedisEventRepository;

class EventRepositoryFactory
{
    public static function create(): EventRepositoryInterface
    {
        $dotEnvLoader = new DotEnvLoader();
        $eventStorage = EventStorageType::tryFrom($dotEnvLoader->getEnv('EVENT_STORAGE') ?? '');

        switch ($eventStorage) {
            case EventStorageType::Memcached:
                $memcachedHost = $dotEnvLoader->getEnv('MEMCACHED_HOST');
                $memcachedPort = $dotEnvLoader->getEnv('MEMCACHED_PORT');
                return new MemcachedEventRepository($memcachedHost, (int) $memcachedPort);
            default:
                $redisHost = $dotEnvLoader->getEnv('REDIS_HOST');
                return new RedisEventRepository($redisHost);
        }
    }
}
