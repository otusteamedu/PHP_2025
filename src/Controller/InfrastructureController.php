<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\InfrastructureHealthCheck;

class InfrastructureController
{
    public function checkServiceHealth(): void
    {
        $infrastructureHealthCheck = new InfrastructureHealthCheck();

        $postgresMsg = $infrastructureHealthCheck->checkPostgresHealth();
        $redisMsg = $infrastructureHealthCheck->checkRedisHealth();
        $memcachedMsg = $infrastructureHealthCheck->checkMemcachedHealth();

        include __DIR__ . '/../../templates/infrastructure.php';
    }
}
