<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\SystemHealth;

use App\Core\Http\Controller\Base\AbstractController;
use App\Core\Http\Message\Response;
use App\Domain\SystemHealth\SystemHealthCheckService;

class SystemHealthController extends AbstractController
{
    public function __construct(
        private readonly SystemHealthCheckService $healthCheckService,
    ) {
        ini_set('session.serialize_handler', 'php_serialize');
        session_start();
    }

    public function checkSystemHealth(): Response
    {
        $result = $this->healthCheckService->checkAll();

        return $this->json(['result' => $result]);
    }
}
