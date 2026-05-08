<?php

declare(strict_types=1);

namespace App\Infrastructure\Redis;

use Redis;

/**
 * Создает подключение к Redis из настроек окружения
 */
final class RedisConnectionFactory
{
    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly ?string $password,
    ) {
    }

    public function create(): Redis
    {
        $redis = new Redis();
        $redis->connect($this->host, $this->port);

        if ($this->password !== '') {
            $redis->auth($this->password);
        }

        return $redis;
    }
}
