<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage\KeyValue\Driver;

use App\Core\Config\ConfigInterface;

class RedisDriver
{
    private readonly \Redis $redisHandler;

    public function __construct(
        private readonly ConfigInterface $dotEnvConfig,
    ) {
        $host = $this->getRequiredCredential('REDIS_HOST');
        $port = (int) $this->getRequiredCredential('REDIS_PORT');

        $this->redisHandler = new \Redis();
        if (!$this->redisHandler->connect($host, $port)) {
            throw new \RuntimeException("Redis connection failed: unable to connect to $host:$port");
        }
    }

    public function getVersion(): string
    {
        return $this->redisHandler->info('Server')['redis_version'];
    }

    public function getHandler(): \Redis
    {
        return $this->redisHandler;
    }

    private function getRequiredCredential(string $key): mixed
    {
        if (!$this->dotEnvConfig->has($key)) {
            throw new \RuntimeException("Missing configuration credential: $key");
        }

        return $this->dotEnvConfig->get($key);
    }
}
