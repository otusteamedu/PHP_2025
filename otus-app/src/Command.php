<?php

namespace App;

use App\Controller\ConsumeReport;
use App\Enum\QueueNameEnum;
use App\Service\RabbitService;
use App\Service\ReportService;
use Exception;
use Throwable;

class Command
{
    public function run(array $argv): void
    {
        try {
            $action = $argv[0] ?? null;
            $subAction = $argv[1] ?? null;

            switch ($action) {
                case 'consumer:run':
                    switch ($subAction) {
                        case QueueNameEnum::REPORT_QUEUE->value:
                            $queueService = new RabbitService();

                            $queueService->consume(QueueNameEnum::REPORT_QUEUE, function ($data) {
                                $reportService = new ReportService(new RabbitService());
                                $reportConsumer = new ConsumeReport($reportService);

                                $reportConsumer->process($data);
                            });

                            break;
                        default:
                            throw new Exception("Unknown subaction: $subAction");
                    }

                default:
                    throw new Exception("Unknown action: $action");
            }
        } catch (Throwable) {
            http_response_code(400);
            echo 'Invalid request';
        }
    }
}
