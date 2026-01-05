<?php

declare(strict_types=1);

namespace App\Storage;

use Memcached;

class MemcachedEventsStorage implements EventsStorageInterface
{
    use EventsMatcherTrait;

    private const EVENTS_KEY = 'events:list';

    private Memcached $client;

    public function __construct(?Memcached $client = null)
    {
        if ($client instanceof Memcached) {
            $this->client = $client;
            return;
        }

        $host = getenv('MEMCACHED_HOST') ?: 'memcached';
        $port = (int)(getenv('MEMCACHED_PORT') ?: 11211);

        $memcached = new Memcached();
        $memcached->addServer($host, $port);

        $this->client = $memcached;
    }

    public function addEvent(array $event): void
    {
        $events = $this->loadEvents();
        $events[] = $event;

        $this->client->set(self::EVENTS_KEY, json_encode($events, JSON_UNESCAPED_UNICODE));
    }

    public function clear(): void
    {
        $this->client->delete(self::EVENTS_KEY);
    }

    public function findMatches(array $params): array
    {
        $events = $this->loadEvents();

        return $this->filterAndSort($events, $params);
    }

    private function loadEvents(): array
    {
        $stored = $this->client->get(self::EVENTS_KEY);

        if ($stored === false && $this->client->getResultCode() !== Memcached::RES_SUCCESS) {
            return [];
        }

        $events = json_decode((string)$stored, true);

        return is_array($events) ? $events : [];
    }
}
