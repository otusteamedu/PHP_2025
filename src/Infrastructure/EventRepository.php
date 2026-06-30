<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\Event;
use App\Domain\EventRepositoryInterface;

class EventRepository implements EventRepositoryInterface
{
    public function __construct(
        private readonly RedisClient $redis
    ) {
    }

    /**
     * @throws \RedisException
     */
    public function nextId(): int
    {
        return (int)$this->redis
            ->getConnection()
            ->incr('event:id');
    }

    /**
     * @throws \RedisException
     */
    public function save(Event $event): void
    {
        $this->redis
            ->getConnection()
            ->set(
                'event:' . $event->getId(),
                json_encode([
                    'id' => $event->getId(),
                    'priority' => $event->getPriority(),
                    'conditions' => $event->getConditions(),
                    'event' => $event->getEventData(),
                ])
            );
    }

    /**
     * @throws \RedisException
     */
    public function clear(): void
    {
        $redis = $this->redis->getConnection();

        $keys = $redis->keys('event:*');

        foreach ($keys as $key) {
            $redis->del($key);
        }
    }

    /**
     * @throws \RedisException
     */
    public function getAll(): array
    {
        $redis = $this->redis->getConnection();

        $keys = $redis->keys('event:*');

        $events = [];

        foreach ($keys as $key) {
            $data = json_decode(
                $redis->get($key),
                true,
            );

            $isEvent = isset($data['id'])
                && isset($data['priority'])
                && isset($data['conditions'])
                && isset($data['event']);

            if (!$isEvent) {
                continue;
            }

            $events[] = new Event(
                $data['id'],
                $data['priority'],
                $data['conditions'],
                $data['event'],
            );
        }

        return $events;
    }

    /**
     * @throws \RedisException
     */
    public function findByParams(array $params): ?Event
    {
        $matchedEvents = [];

        foreach ($this->getAll() as $event) {

            $matched = true;

            foreach ($event->getConditions() as $key => $value) {

                if (
                    !array_key_exists($key, $params)
                    || $params[$key] !== $value
                ) {
                    $matched = false;
                    break;
                }
            }

            if ($matched) {
                $matchedEvents[] = $event;
            }
        }

        usort(
            $matchedEvents,
            static fn (Event $a, Event $b): int =>
                $b->getPriority() <=> $a->getPriority()
        );

        return $matchedEvents[0] ?? null;
    }
}