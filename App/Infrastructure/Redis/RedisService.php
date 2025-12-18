<?php

declare(strict_types=1);

namespace App\Infrastructure\Redis;

class RedisService extends \Redis
{
    public function __construct()
    {
        $projectName = \getenv('PROJECT_NAME');
        $host = \getenv('REDIS_CONTAINER_NAME');
        $port = (int)\getenv('REDIS_PORT');

        $this->connect($projectName . '_' . $host, $port);
    }
}
