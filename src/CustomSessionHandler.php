<?php

declare(strict_types=1);

namespace App;

use RedisCluster;
use RedisClusterException;
use SessionHandlerInterface;

class CustomSessionHandler implements SessionHandlerInterface
{
    private RedisCluster $redis;

    /**
     * @throws RedisClusterException
     */
    public function __construct()
    {
        $this->redis = new RedisCluster(null, [
            'redis-node-1:6379',
            'redis-node-2:6379',
            'redis-node-3:6379',
            'redis-node-4:6379',
            'redis-node-5:6379',
            'redis-node-6:6379',
        ]);

        session_set_save_handler(
            [$this, 'open'],
            [$this, 'close'],
            [$this, 'read'],
            [$this, 'write'],
            [$this, 'destroy'],
            [$this, 'gc']
        );
    }

    public function startSession(): void
    {
        session_start();
    }

    public function closeSession(): void
    {
        session_write_close();
    }

    public function open($path, $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($id): string|false
    {
        $sessionData = $this->redis->get($id);

        return $sessionData ? $sessionData : '';
    }

    public function write($id, $data): bool
    {
        return $this->redis->set($id, $data);
    }

    public function destroy($id): bool
    {
        return (bool) $this->redis->del($id);
    }

    public function gc($max_lifetime): int|false
    {
        return true;
    }
}