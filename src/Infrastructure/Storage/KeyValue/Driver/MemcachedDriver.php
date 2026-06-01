<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage\KeyValue\Driver;

use App\Core\Container\Config\Data\DotEnv\DotEnvConfigInterface;

class MemcachedDriver
{
    private readonly \Memcached $memcachedHandler;

    public function __construct(
        private readonly DotEnvConfigInterface $dotEnvConfig,
    ) {
        $host = $this->getRequiredCredential('MEMCACHED_HOST');
        $port = (int) $this->getRequiredCredential('MEMCACHED_PORT');

        $this->memcachedHandler = new \Memcached();
        if (!$this->memcachedHandler->addServer($host, $port)) {
            throw new \RuntimeException("Memcached connection failed to $host:$port");
        }
    }

    public function getVersion(): string
    {
        return $this->memcachedHandler->getVersion()['memcached:11211'];
    }

    public function getHandler(): \Memcached
    {
        return $this->memcachedHandler;
    }

    private function getRequiredCredential(string $key): mixed
    {
        if (!$this->dotEnvConfig->has($key)) {
            throw new \RuntimeException("Missing configuration credential: $key");
        }

        return $this->dotEnvConfig->get($key);
    }
}
