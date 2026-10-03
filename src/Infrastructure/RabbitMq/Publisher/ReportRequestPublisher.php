<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Publisher;

use App\Core\Container\Config\Contracts\DotEnvConfigInterface;
use App\Domain\BankReport\Contract\ReportRequestPublisherInterface;
use App\Domain\BankReport\Exception\ReportRequestException;
use App\Domain\BankReport\ValueObject\ReportGenerationRequest;
use App\Infrastructure\RabbitMq\Exception\AmqpConnectionException;
use App\Infrastructure\RabbitMq\Message\ReportGenerationMessage;
use App\Infrastructure\RabbitMq\Producer\RabbitMqProducer;

class ReportRequestPublisher implements ReportRequestPublisherInterface
{
    private readonly string $queueName;

    public function __construct(
        private readonly RabbitMqProducer $producer,
        DotEnvConfigInterface $config,
    ) {
        $this->queueName = $config->get('RABBITMQ_BANK_REPORTS_QUEUE', 'bank_reports');
    }

    public function publish(ReportGenerationRequest $request): void
    {
        try {
            $message = $this->buildReportGenerationMessage($request);
            $body = json_encode($message->toArray(), JSON_THROW_ON_ERROR);

            $this->producer->publish(
                body: $body,
                routingKey: $this->queueName,
            );
        } catch (AmqpConnectionException | \JsonException $e) {
            throw ReportRequestException::requestNotAccepted($e);
        }
    }

    private function buildReportGenerationMessage(ReportGenerationRequest $request): ReportGenerationMessage
    {
        return new ReportGenerationMessage(
            reportId: $request->reportId->value,
            clientName: $request->clientName->value,
            dateFrom: $request->dateRange->dateFrom->format('Y-m-d'),
            dateTo: $request->dateRange->dateTo->format('Y-m-d'),
            reportType: $request->reportType->value,
            email: $request->email->value,
        );
    }
}
