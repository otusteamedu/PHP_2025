<?php

declare(strict_types=1);

namespace App\Service;

use App\Infrastructure\RedisDriver;

class SessionStorageChecker
{
    private readonly \Redis $redisHandler;

    public function __construct()
    {
        $this->redisHandler = new RedisDriver()->getHandler();
    }

    public function getSessionVarsFromStorage(): array
    {
        $sessionId = session_id();
        if ($sessionId === '' || $sessionId === false) {
            return [];
        }

        $sessionVarsSerialized = $this->redisHandler->get("PHPREDIS_SESSION:$sessionId");
        if ($sessionVarsSerialized === false) {
            return [];
        }

        $sessionVars = unserialize($sessionVarsSerialized);

        return is_array($sessionVars) && !empty($sessionVars) ? $sessionVars : [];
    }
}
