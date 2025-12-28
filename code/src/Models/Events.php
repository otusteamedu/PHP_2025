<?php
namespace Ak\Hw\Models;
use Predis\Client;

class Events
{
    protected static ?Client $client = null;

    private static function getClient(): Client
    {
        if (self::$client === null) {
            self::$client = new Client([
                'scheme' => 'tcp',
                'host'   => 'redis-server',
                'port'   => 6379,
            ]);
        }
        return self::$client;
    }

    public function getParams(): array
    {
        return self::getClient()->zrange('events:params', 0, -1, ['withscores' => true]);
    }

    public function addParam(string $name, int $value): void
    {
        self::getClient()->zadd('events:params', [$name => $value]);
    }

    public function deleteParam(string $name): void
    {
        self::getClient()->zrem('events:params', $name);
    }

    public function deleteAllParams(): void
    {
        self::getClient()->del('events:params');
    }

    public function getScore(array $params): int
    {
        $result = 0;
        foreach ($params as $name) {
            $score = self::getClient()->zscore('events:params', $name);
            if ($score !== null) {
                $result += (int)$score;
            }
        }

        return $result;
    }
}