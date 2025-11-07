<?php

namespace App;

use App\Controller\CreateReport;
use App\Service\RabbitService;
use App\Service\ReportService;
use Exception;
use Throwable;

class App
{
    public function run(): void
    {
        try {
            $queueService = new RabbitService();
            $reportService = new ReportService($queueService);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $body = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);

                $app = new CreateReport($reportService);
                $app->process($body);

                http_response_code(200);
                echo 'Report in progress';
            } else {
                throw new Exception('Invalid request method');
            }
        } catch (Throwable) {
            http_response_code(400);
            echo 'Invalid request';
        }
    }
}
