<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\ServicesHealth;

use App\Controller\Http\AbstractController;
use App\Core\Http\Response;
use App\Domain\SystemHealth\SystemHealthCheckService;

class ServicesHealthController extends AbstractController
{
    public function __construct()
    {
        ini_set('session.serialize_handler', 'php_serialize');
        session_start();

        parent::__construct();
    }

    public function checkServicesHealth(): Response
    {
        $result = new SystemHealthCheckService()->checkAll();

        return new Response(json_encode($result), 200, ['Content-Type: application/json; charset=utf-8']);
    }
}
