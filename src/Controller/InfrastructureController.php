<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Response;
use App\Service\InfrastructureHealthCheck;

class InfrastructureController extends AbstractController
{
    public function checkServiceHealth(): Response
    {
        $infrastructureHealthCheck = new InfrastructureHealthCheck();

        $this->view->postgresMsg = $infrastructureHealthCheck->checkPostgresHealth();
        $this->view->redisMsg = $infrastructureHealthCheck->checkRedisHealth();
        $this->view->memcachedMsg = $infrastructureHealthCheck->checkMemcachedHealth();

        return $this->view->render('infrastructure.php');
    }
}
