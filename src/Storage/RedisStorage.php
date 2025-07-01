<?php

namespace App\Storage;

use Predis\Client;

class RedisStorage implements StorageInterface {
	private $client;
    private string $zsetKey = 'events';
    private string $hashKey = 'event_data';

	public function __construct() {
		$this->client = new Client([
			'scheme' => 'tcp',
			'host'   => $_ENV['REDIS_HOST'] ?: 'redis',
			'port'   => $_ENV['REDIS_PORT'] ?: 6379,
		]);
	}

	public function addEvent(array $event): void {
		$id = uniqid('event_', true);
		$this->client->zadd('events', [$id => $event['priority']]);
		$this->client->hset('event_data', $id, json_encode($event));
	}

	public function clearEvents(): void {
		$this->client->del(['events', 'event_data']);
	}

    public function findBestMatch(array $params): ?array
    {
        // Получаем события в порядке убывания priority
        $ids = $this->client->zrevrange($this->zsetKey, 0, -1);

        foreach ($ids as $id) {
            $eventJson = $this->client->hget($this->hashKey, $id);

            if (!$eventJson) continue;

            $event = json_decode($eventJson, true);
            if (!$event) continue;

            // Проверяем условия: все пары conditions должны быть выполнены в $params
            $conditions = $event['conditions'] ?? [];

            if ($this->conditionsMatch($conditions, $params)) {
                return $event;
            }
        }
        return null;
    }

    private function conditionsMatch(array $conditions, array $params): bool
    {
        foreach ($conditions as $key => $value) {
            // Если параметр отсутствует или не совпадает — не подходит
            if (!array_key_exists($key, $params['params']) || $params['params'][$key] != $value) {
                return false;
            }
        }
        return true;
    }
}