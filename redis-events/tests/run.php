<?php

declare(strict_types=1);

use App\Domain\Event;
use App\Storage\EventStorageInterface;
use App\Storage\InMemoryEventStorage;
use App\Storage\RedisEventStorage;
use Predis\Client;

require dirname(__DIR__) . '/vendor/autoload.php';

/** @param EventStorageInterface[] $storages */
foreach ([
    'in-memory' => new InMemoryEventStorage(),
    'redis' => new RedisEventStorage(new Client(getenv('REDIS_DSN') ?: 'redis://127.0.0.1:6381/0')),
] as $name => $storage) {
    $storage->clear();
    $storage->add(Event::create(1000, ['param1' => 1], ['name' => 'first']));
    $storage->add(Event::create(2000, ['param1' => 2, 'param2' => 2], ['name' => 'second']));
    $storage->add(Event::create(3000, ['param1' => 1, 'param2' => 2], ['name' => 'third']));
    $storage->add(Event::create(4000, ['enabled' => true], ['name' => 'enabled']));

    assertPriority($storage, ['param1' => 1, 'param2' => 2], 3000, $name);
    assertPriority($storage, ['param1' => 1], 1000, $name);
    assertPriority($storage, ['param1' => 2, 'param2' => 2], 2000, $name);
    assertPriority($storage, ['enabled' => true], 4000, $name);

    $storage->clear();
    if ($storage->findBest(['param1' => 1]) !== null) {
        throw new RuntimeException(sprintf('%s: clear() не удалил события.', $name));
    }
}

echo "OK: Redis и InMemory реализации прошли одинаковые сценарии.\n";

/** @param array<string, scalar> $params */
function assertPriority(EventStorageInterface $storage, array $params, int $expected, string $storageName): void
{
    $event = $storage->findBest($params);
    if ($event?->priority !== $expected) {
        throw new RuntimeException(sprintf('%s: ожидался priority %d.', $storageName, $expected));
    }
}
