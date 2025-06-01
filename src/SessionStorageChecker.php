<?php

declare(strict_types=1);

namespace App;

class SessionStorageChecker
{
    private readonly \Redis $redisHandler;
    private array $sessionVars = [];

    public function __construct()
    {
        $this->redisHandler = new RedisDriver()->getHandler();
    }

    public function run(): self
    {
        $this->sessionVars = $this->getSessionVarsFromStorage();

        return $this;
    }

    public function getSessionVars(): array
    {
        return $this->sessionVars;
    }

    private function getSessionVarsFromStorage(): array
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
