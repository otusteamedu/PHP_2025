<?php

namespace App\Repositories;

use App\DTO\EventDTO;
use App\Tasks\GetEventHashTask;
use Exception;
use Redis;
use RedisException;

class RedisRepository extends Repository
{
    /** @var Redis */
    public Redis $client;

    /**
     * @throws RedisException
     * @throws Exception
     */
    public function __construct() {
        $this->connect();
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function connect(): void {
        $host = getenv('REDIS_HOST');
        $port = 6379;
        $password = getenv('REDIS_PASSWORD');

        try {
            $redis = new Redis();
            $redis->connect($host, $port);
            $redis->auth($password);
        } catch (RedisException $e) {
            throw new Exception($e);
        }

        $this->client = $redis;
    }

    /**
     * @param EventDTO $dto
     * @return array|null
     * @throws RedisException
     */
    public function getEvent(EventDTO $dto): ?array {
        $hash = (new GetEventHashTask())->run($dto->params);
        $key = "event:$hash";

        $events = $this->client->hGetAll($key);

        if (empty($events) === false) {
            $maxPriority = max(array_keys($events));
            $event = json_decode($events[$maxPriority], true);
            $this->client->hDel($key, $maxPriority);
        }

        return $event ?? [];
    }

    /**
     * @param EventDTO $dto
     * @return void
     * @throws RedisException
     */
    public function createEvent(EventDTO $dto): void {
        $hash = (new GetEventHashTask())->run($dto->params);
        $key = "event:$hash";

        $priority = $dto->priority;

        $this->client->hSet($key, $priority, json_encode($dto->event));
    }

    /**
     * @return void
     * @throws RedisException
     */
    public function truncateEvent(): void {
        $keys = $this->client->keys('event:*');
        $this->client->del($keys);
    }
}