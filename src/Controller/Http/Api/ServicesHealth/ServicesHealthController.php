<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\ServicesHealth;

use App\Controller\Http\AbstractController;
use App\Core\Http\Message\Response;
use App\Domain\SystemHealth\SystemHealthCheckService;

class ServicesHealthController extends AbstractController
{
    public function __construct(
        private readonly SystemHealthCheckService $healthCheckService,
    ) {
        ini_set('session.serialize_handler', 'php_serialize');
        session_start();
    }

    public function checkServicesHealth(): Response
    {
        $result = $this->healthCheckService->checkAll();

        return $this->json(['result' => $result]);
    }
}
