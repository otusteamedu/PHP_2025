<?php

namespace Pryaniki\App;
class Auth
{
    private \Redis $redis;

    public function __construct()
    {
        session_start();
        $this->increaseSessionCounter();
        $this->redis =  new \Redis();
    }

    private function increaseSessionCounter(): void
    {
        if (!isset($_SESSION['counter'])) {
            $_SESSION['counter'] = 1;
        } else {
            $_SESSION['counter']++;
        }
    }

    public function getSessionId(): string
    {
        return session_id();
    }

    public function getContainerName(): string
    {
        return $_SERVER['HOSTNAME'];
    }

    public function getSessionCounter(): string
    {
        return $_SESSION['counter'];
    }

    public function getRedisContainerId(): string
    {
        $this->redis->connect('redis', 6379);
        $containerIdFromRedis = $this->redis->get('container_id');
        if (!$containerIdFromRedis) {
            $this->redis->set('container_id', $_SERVER['HOSTNAME']);
            $containerIdFromRedis = $this->redis->get('container_id');
        }
        return $containerIdFromRedis;
    }
}