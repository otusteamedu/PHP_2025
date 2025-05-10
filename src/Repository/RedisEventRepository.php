<?php
namespace App\Repository;

use Predis\Client;

class RedisEventRepository implements EventRepositoryInterface {
    private Client $redis;

    public function __construct(Client $redis) {
        $this->redis = $redis;
    }

    public function addEvent(array $event): void {
        $id = uniqid('event_', true);
        $this->redis->set($id, json_encode($event));
    }

    public function clearEvents(): void {
        $keys = $this->redis->keys('event_*');
        foreach ($keys as $key) {
            $this->redis->del($key);
        }
    }

    public function findMatchingEvent(array $params): ?array {
        $keys = $this->redis->keys('event_*');
        $matched = [];

        foreach ($keys as $key) {
            $event = json_decode($this->redis->get($key), true);
            $conditions = $event['conditions'] ?? [];

            $allMatch = true;
            foreach ($conditions as $condKey => $condValue) {
                if (!isset($params[$condKey]) || $params[$condKey] != $condValue) {
                    $allMatch = false;
                    break;
                }
            }

            if ($allMatch) {
                $matched[] = $event;
            }
        }

        if (empty($matched)) {
            return null;
        }

        usort($matched, fn($a, $b) => $b['priority'] <=> $a['priority']);
        return $matched[0];
    }
}