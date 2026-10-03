<?php

declare(strict_types=1);

namespace App\Controller\Amqp\BankReport;

use App\Domain\BankReport\ReportService;
use App\Domain\BankReport\ValueObject\ClientName;
use App\Domain\BankReport\ValueObject\DateRange;
use App\Domain\BankReport\ValueObject\Email;
use App\Domain\BankReport\ValueObject\ReportGenerationRequest;
use App\Domain\BankReport\ValueObject\ReportId;
use App\Domain\BankReport\ValueObject\ReportType;
use App\Infrastructure\RabbitMq\Connection\AmqpConnectionInterface;
use App\Infrastructure\RabbitMq\Consumer\AbstractConsumer;
use App\Infrastructure\RabbitMq\Consumer\HandleResult;
use App\Infrastructure\RabbitMq\Message\ReportGenerationMessage;
use PhpAmqpLib\Message\AMQPMessage;

class BankReportConsumer extends AbstractConsumer
{
    private readonly ReportService $reportService;

    public function __construct(
        ReportService $reportService,
        AmqpConnectionInterface $connection,
        string $queueName,
        int $maxAttempts,
        int $prefetchCount,
    ) {
        $this->reportService = $reportService;
        parent::__construct(
            connection: $connection,
            queueName: $queueName,
            maxAttempts: $maxAttempts,
            prefetchCount: $prefetchCount,
        );
    }

    protected function handle(AMQPMessage $msg): HandleResult
    {
        try {
            $data = json_decode($msg->getBody(), true, 512, JSON_THROW_ON_ERROR);
            $message = ReportGenerationMessage::fromArray($data);
        } catch (\JsonException $e) {
            return HandleResult::drop('Invalid JSON: ' . $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            return HandleResult::drop('Invalid payload: ' . $e->getMessage());
        }

        $this->logProcessing($message);

        try {
            $reportRequest = new ReportGenerationRequest(
                reportId: ReportId::fromString($message->reportId),
                clientName: new ClientName($message->clientName),
                dateRange: new DateRange($message->dateFrom, $message->dateTo),
                reportType: ReportType::from($message->reportType),
                email: new Email($message->email),
            );
            $this->reportService->generateReport($reportRequest);

            return HandleResult::ack();
        } catch (\RuntimeException $e) {
            return HandleResult::reject($e->getMessage(), $e->getCode());
        } catch (\Throwable $e) {
            return HandleResult::drop($e->getMessage(), $e->getCode());
        }
    }

    private function logProcessing(ReportGenerationMessage $message): void
    {
        fwrite(STDOUT, sprintf(
            " [PROCESSING] (attempt %d/%d) Client: %s | Period: %s - %s | Type: %s | Email: %s\n",
            $this->getAttemptNumber(),
            $this->getMaxAttempts(),
            $message->clientName,
            $message->dateFrom,
            $message->dateTo,
            $message->reportType,
            $message->email,
        ));
    }
}
