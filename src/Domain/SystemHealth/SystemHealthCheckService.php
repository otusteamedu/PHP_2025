<?php

declare(strict_types=1);

namespace App\Domain\SystemHealth;

use App\Domain\SystemHealth\Enum\ServiceType;
use App\Domain\SystemHealth\Model\HealthStatus;
use App\Infrastructure\Database\Connection\PDOWrapper;
use App\Infrastructure\Storage\KeyValue\Driver\MemcachedDriver;
use App\Infrastructure\Storage\KeyValue\Driver\RedisDriver;

class SystemHealthCheckService
{
    public function __construct(
        private readonly PDOWrapper $pdoWrapper,
        private readonly RedisDriver $redisDriver,
        private readonly MemcachedDriver $memcachedDriver,
        private readonly SessionStorageChecker $sessionStorageChecker,
    ) {
    }

    public function checkAll(): array
    {
        return [
            ServiceType::POSTGRESQL->value => $this->checkPostgres(),
            ServiceType::REDIS->value => $this->checkRedis(),
            ServiceType::MEMCACHED->value => $this->checkMemcached(),
            ServiceType::SESSION_STORAGE->value => $this->checkSessionStorage(),
        ];
    }

    private function checkPostgres(): HealthStatus
    {
        try {
            $version = $this->pdoWrapper->getHandler()->query('SELECT version();')->fetch(\PDO::FETCH_COLUMN);
            return new HealthStatus(
                isHealthy: true,
                message: 'PostgreSQL is healthy',
                details: ['version' => $version],
            );
        } catch (\PDOException $e) {
            return new HealthStatus(
                isHealthy: false,
                message: 'PostgreSQL connection failed',
                details: [
                    'error' => $e->getMessage(),
                    'code' => $e->getCode(),
                ],
            );
        }
    }

    private function checkRedis(): HealthStatus
    {
        try {
            $redisHandler = $this->redisDriver->getHandler();
            $ping = $redisHandler->ping('PONG');
            $version = $this->redisDriver->getVersion();
            return new HealthStatus(
                isHealthy: $ping === 'PONG',
                message: $ping === 'PONG' ? 'Redis is healthy' : 'Redis responded with unexpected value',
                details: [
                    'pingResponse' => $ping,
                    'version' => $version,
                ],
            );
        } catch (\RuntimeException $e) {
            return new HealthStatus(
                isHealthy: false,
                message: 'Redis connection failed',
                details: ['error' => $e->getMessage()],
            );
        }
    }

    private function checkMemcached(): HealthStatus
    {
        $startTime = microtime(true);

        try {
            $version = $this->memcachedDriver->getVersion();
            $responseTime = microtime(true) - $startTime;
            return new HealthStatus(
                isHealthy: true,
                message: 'Memcached is healthy',
                details: [
                    'version' => $version,
                    'response_time_ms' => round($responseTime * 1000, 2)
                ],
            );
        } catch (\RuntimeException $e) {
            $responseTime = microtime(true) - $startTime;
            return new HealthStatus(
                isHealthy: false,
                message: 'Memcached connection failed',
                details: [
                    'error' => $e->getMessage(),
                    'response_time_ms' => round($responseTime * 1000, 2)
                ],
            );
        }
    }

    private function checkSessionStorage(): HealthStatus
    {
        $startTime = microtime(true);

        try {
            $_SESSION['health_check_timestamp'] = time();
            $_SESSION['health_check_value'] = 'session_health_check';

            session_write_close();
            usleep(10000);

            $storedSessionVars = $this->sessionStorageChecker->getSessionVarsFromStorage();

            $responseTime = microtime(true) - $startTime;

            $isHealthy = isset($storedSessionVars['health_check_timestamp'])
                && isset($storedSessionVars['health_check_value'])
                && $storedSessionVars['health_check_value'] === 'session_health_check';

            return new HealthStatus(
                isHealthy: $isHealthy,
                message: $isHealthy
                    ? 'Session storage (Redis) is working correctly'
                    : 'Session data not properly stored or retrieved',
                details: [
                    'response_time_ms' => round($responseTime * 1000, 2),
                    'test_data_written' => true,
                    'data_retrieved' => $storedSessionVars,
                    'redis_connection_ok' => !empty($storedSessionVars),
                ],
            );
        } catch (\Exception $e) {
            $responseTime = microtime(true) - $startTime;
            return new HealthStatus(
                isHealthy: false,
                message: 'Session storage check failed',
                details: [
                    'error' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'response_time_ms' => round($responseTime * 1000, 2)
                ]
            );
        }
    }
}
