<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Application\DotEnvLoader;
use App\Infrastructure\Enum\EventStorage;

class EventRepositoryFactory
{
    public static function create(): EventRepositoryInterface
    {
        $dotEnvLoader = new DotEnvLoader();
        $eventStorage = EventStorage::tryFrom($dotEnvLoader->getEnv('EVENT_STORAGE') ?? '');

        switch ($eventStorage) {
            case EventStorage::Memcached:
                // TODO
            default:
                $redisHost = $dotEnvLoader->getEnv('REDIS_HOST');
                return new RedisEventRepository($redisHost);
        }
    }
}
