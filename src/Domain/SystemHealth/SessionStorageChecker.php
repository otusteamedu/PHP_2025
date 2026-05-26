<?php

declare(strict_types=1);

namespace App\Domain\SystemHealth;

use App\Infrastructure\Storage\KeyValue\Driver\RedisDriver;

class SessionStorageChecker
{
    public function __construct(
        private readonly RedisDriver $redisDriver,
    ) {
    }

    public function getSessionVarsFromStorage(): array
    {
        $sessionId = session_id();
        if ($sessionId === '' || $sessionId === false) {
            return [];
        }

        $sessionVarsSerialized = $this->redisDriver->getHandler()->get("PHPREDIS_SESSION:$sessionId");
        if ($sessionVarsSerialized === false) {
            return [];
        }

        $sessionVars = unserialize($sessionVarsSerialized);

        return is_array($sessionVars) && !empty($sessionVars) ? $sessionVars : [];
    }
}
