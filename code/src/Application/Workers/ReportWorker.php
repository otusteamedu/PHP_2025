<?php

declare(strict_types=1);

namespace App\Application\Workers;

use App\Domain\Interfaces\EmailSenderInterface;
use App\Domain\Interfaces\MessageConsumerInterface;
use App\Domain\Interfaces\ReportGeneratorInterface;

class ReportWorker
{
    private const QUEUE_NAME = 'bank_reports';

    public function __construct(
        private readonly MessageConsumerInterface $consumer,
        private readonly ReportGeneratorInterface $reportGenerator,
        private readonly EmailSenderInterface $emailSender
    ) {}

    public function run(): void
    {
        $this->consumer->consume(self::QUEUE_NAME, function (array $message) {
            $this->processMessage($message);
        });
    }

    private function processMessage(array $message): void
    {
        $name = $message['name'] ?? 'Unknown';
        $email = $message['email'] ?? '';
        $year = (int)($message['year'] ?? date('Y'));
        $createdAt = $message['created_at'] ?? date('Y-m-d H:i:s');

        if (empty($email)) {
            echo "  [ERROR] Email is empty, skipping...\n\n";
            return;
        }

        $report = $this->reportGenerator->generate($name, $email, $year);

        $subject = "Банковская выписка за {$year} год";

        $this->emailSender->send($email, $subject, $report);
    }
}
