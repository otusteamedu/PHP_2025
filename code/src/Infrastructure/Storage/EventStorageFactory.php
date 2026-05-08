<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Domain\Storage\EventStorageInterface;
use App\Infrastructure\Redis\RedisConnectionFactory;
use App\Infrastructure\Redis\RedisEventStorage;
use InvalidArgumentException;

/**
 * Фабрика выбора хранилища событий по настройке EVENT_STORAGE_DRIVER
 */
final class EventStorageFactory
{
    /**
     * Создает реализацию хранилища событий для выбранного драйвера
     */
    public function create(): EventStorageInterface
    {
        $driver = getenv('EVENT_STORAGE_DRIVER') ?: 'redis';

        return match ($driver) {
            'redis' => $this->createRedisStorage(),
            default => throw new InvalidArgumentException(
                'Unsupported event storage driver "' . $driver . '".',
            ),
        };
    }

    private function createRedisStorage(): EventStorageInterface
    {
        $factory = new RedisConnectionFactory(
            getenv('REDIS_APP') ?: 'redis',
            (int) (getenv('REDIS_PORT') ?: 6379),
            getenv('REDIS_PASSWORD') ?: '',
        );

        return new RedisEventStorage($factory->create());
    }
}
