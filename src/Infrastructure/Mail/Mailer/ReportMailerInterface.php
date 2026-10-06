<?php

declare(strict_types=1);

namespace App\Infrastructure\Mail\Mailer;

use App\Domain\BankReport\ValueObject\Email;
use App\Domain\BankReport\ValueObject\Report;
use App\Infrastructure\Mail\Exception\ReportMailerException;

interface ReportMailerInterface
{
    /**
     * @throws ReportMailerException
     */
    public function send(Report $report, Email $email): void;
}
