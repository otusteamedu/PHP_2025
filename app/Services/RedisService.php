<?php

namespace App\Services;

use Exception;
use Redis;
use RedisException;

class RedisService extends Service
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
     * @param array $data
     * @return string
     */
    protected function getEventHash(array $data): string {
        ksort($data);
        return md5(serialize($data));
    }

    /**
     * @param array $data
     * @return array|null
     * @throws RedisException
     */
    public function getEvent(array $data): ?array {
        $hash = $this->getEventHash($data);
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
     * @param array $data
     * @return void
     * @throws RedisException
     */
    public function createEvent(array $data): void {
        $hash = $this->getEventHash($data['conditions']);
        $key = "event:$hash";

        $priority = $data['priority'];

        $this->client->hSet($key, $priority, json_encode($data['event']));
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