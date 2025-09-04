<?php

namespace App\Session;

use App\Interface\SessionManagerInterface;
use Redis;

class RedisSessionManager implements SessionManagerInterface
{
    private Redis $redis;
    private int $ttl = 3600; // время жизни токена в секундах (1 час)
    private string $sessionId;

    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect('redis');

        // Генерируем/берём идентификатор сессии (обычно через cookie)
        if (!isset($_COOKIE['SESSION_ID'])) {
            $this->sessionId = bin2hex(random_bytes(16));
            setcookie('SESSION_ID', $this->sessionId, time() + $this->ttl, '/', '', false, true);
        } else {
            $this->sessionId = $_COOKIE['SESSION_ID'];
        }
    }

    public function generateCsrfToken(): string
    {
        $key = $this->getTokenKey();

        $token = $this->redis->get($key);
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            $this->redis->setex($key, $this->ttl, $token);
        }

        return $token;
    }

    public function verifyCsrfToken(string $token): bool
    {
        $key = $this->getTokenKey();
        $storedToken = $this->redis->get($key);
        return $storedToken && hash_equals($storedToken, $token);
    }

    private function getTokenKey(): string
    {
        return "csrf_token:{$this->sessionId}";
    }
}