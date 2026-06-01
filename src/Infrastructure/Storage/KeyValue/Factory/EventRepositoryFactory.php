<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage\KeyValue\Factory;

use App\Core\Container\Config\Data\DotEnv\DotEnvConfigInterface;
use App\Domain\EventSystem\Interface\EventRepositoryInterface;
use App\Infrastructure\Storage\KeyValue\Driver\MemcachedDriver;
use App\Infrastructure\Storage\KeyValue\Driver\RedisDriver;
use App\Infrastructure\Storage\KeyValue\Repository\MemcachedEventRepository;
use App\Infrastructure\Storage\KeyValue\Repository\RedisEventRepository;

class EventRepositoryFactory
{
    public function __construct(
        private readonly DotEnvConfigInterface $dotEnvConfig,
        private readonly RedisDriver $redisDriver,
        private readonly MemcachedDriver $memcachedDriver,
    ) {
    }

    public function create(): EventRepositoryInterface
    {
        $eventStorage = EventStorageType::tryFrom($this->dotEnvConfig->get('EVENT_STORAGE') ?? '');

        return match ($eventStorage) {
            EventStorageType::Memcached => new MemcachedEventRepository($this->memcachedDriver),
            default => new RedisEventRepository($this->redisDriver),
        };
    }
}
