<?php

declare(strict_types=1);

namespace App\Service;

use App\Infrastructure\MemcachedDriver;
use App\Infrastructure\PgSQLDriver;
use App\Infrastructure\RedisDriver;

class InfrastructureHealthCheck
{
    public function checkPostgresHealth(): string
    {
        try {
            return new PgSQLDriver()->getVersion();
        } catch (\PDOException $e) {
            return $e->getMessage();
        }
    }

    public function checkRedisHealth(): string
    {
        try {
            return new RedisDriver()->getVersion();
        } catch (\RuntimeException $e) {
            return $e->getMessage();
        }
    }

    public function checkMemcachedHealth(): string
    {
        try {
            return new MemcachedDriver()->getVersion();
        } catch (\RuntimeException $e) {
            return $e->getMessage();
        }
    }
}
