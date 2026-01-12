<?php
namespace App\Repository;

use App\Interfaces\EventRepositoryInterface;
use Predis\client;

class RedisEventRepository implements EventRepositoryInterface {
    private $client;
    private $key = 'analyst_events';

    public function __construct() {
        $this->client = new client(); // Параметры подключения по умолчанию
    }

    public function addEvent(array $event): void {
        $this->client->rpush($this->key, [json_encode($event)]);
    }

    public function clearAll(): void {
        $this->client->del($this->key);
    }

    public function getAllEvents(): array {
        $events = $this->client->lrange($this->key, 0, -1);
        return array_map(fn($e) => json_decode($e, true), $events);
    }
}