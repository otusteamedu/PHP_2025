<?php

declare(strict_types=1);

namespace App;

class InfrastructureHealthCheck
{
    private array $services = [];

    public function run(): self
    {
        $this->postgresHealthCheck();
        $this->redisHealthCheck();
        $this->memcachedHealthCheck();

        return $this;
    }

    public function getServices(): array
    {
        return $this->services;
    }

    private function postgresHealthCheck(): void
    {
        try {
            $this->services['Postgres'] = new PgSQLDriver()->getVersion();
        } catch (\PDOException $e) {
            $this->services['Postgres'] = $e->getMessage();
        }
    }

    private function redisHealthCheck(): void
    {
        try {
            $this->services['Redis'] = new RedisDriver()->getVersion();
        } catch (\RuntimeException $e) {
            $this->services['Redis'] = $e->getMessage();
        }
    }

    private function memcachedHealthCheck(): void
    {
        try {
            $this->services['Memcached'] = new MemcachedDriver()->getVersion();
        } catch (\RuntimeException $e) {
            $this->services['Memcached'] = $e->getMessage();
        }
    }
}
