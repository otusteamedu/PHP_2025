<?php

namespace App;

use App\Controller\CreateReport;
use App\Exception\CustomException;
use App\Service\RabbitService;
use App\Service\ReportService;
use Throwable;

class App
{
    public function run(): string
    {
        try {
            $queueService = new RabbitService();
            $reportService = new ReportService($queueService);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $body = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);

                $app = new CreateReport($reportService);
                $app->process($body);

                http_response_code(200);

                return 'Report in progress';
            }

            throw new CustomException('Not found', 404);
        } catch (CustomException $e) {
            http_response_code($e->getCode());

            return $e->getMessage();
        } catch (Throwable) {
            http_response_code(500);

            return 'Internal server error';
        }
    }
}
