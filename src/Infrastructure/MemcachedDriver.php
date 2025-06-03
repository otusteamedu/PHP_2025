<?php

declare(strict_types=1);

namespace App\Infrastructure;

class MemcachedDriver
{
    private readonly \Memcached $memcachedHandler;

    public function __construct()
    {
        $this->memcachedHandler = new \Memcached();
        if (!$this->memcachedHandler->addServer('memcached', 11211)) {
            throw new \RuntimeException('Connection failed');
        }
    }

    public function getVersion(): string
    {
        return $this->memcachedHandler->getVersion()['memcached:11211'];
    }
}
