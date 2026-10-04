<?php

declare(strict_types=1);

namespace App\Infrastructure\Mail\Mailer;

use App\Domain\BankReport\ValueObject\Email;
use App\Domain\BankReport\ValueObject\Report;
use App\Infrastructure\Mail\Exception\ReportMailerException;

class TemporarilyUnavailableReportMailer implements ReportMailerInterface
{
    /** @var array<string, int> */
    private array $remainingRejects = [];

    public function __construct(
        private readonly ReportMailerInterface $innerMailer,
        private readonly int $maxAttempts,
    ) {
    }

    public function send(Report $report, Email $email): void
    {
        $key = $report->id->value;

        if (!isset($this->remainingRejects[$key])) {
            $this->remainingRejects[$key] = mt_rand(1, $this->maxAttempts - 1);
        }

        if ($this->remainingRejects[$key] > 0) {
            $this->remainingRejects[$key]--;
            throw ReportMailerException::temporarilyUnavailable();
        }

        unset($this->remainingRejects[$key]);

        $this->innerMailer->send($report, $email);
    }
}
