<?php

namespace App\Infrastructure;

use Redis;

class RedisClient
{
    public static function create(): Redis
    {
        $projectName = \getenv('PROJECT_NAME');
        $host = \getenv('REDIS_CONTAINER_NAME');
        $port = (int)\getenv('REDIS_PORT');

        $client = new Redis();
        $client->connect($projectName . '_' . $host, $port);

        return $client;
    }
}
