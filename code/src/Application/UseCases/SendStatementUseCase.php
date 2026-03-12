<?php

declare(strict_types=1);

namespace Queues\Application\UseCases;

use Queues\Domain\Entities\Statement;
use Queues\Application\Interfaces\MailerInterface;

class SendStatementUseCase
{
    public function __construct(
        private readonly MailerInterface $mailer
    ) {
    }

    public function execute(Statement $statement): void
    {
        $subject = 'Банковская выписка за период ' . $statement->dateFrom . ' - ' . $statement->dateTo;
        $body = $statement->content;

        $this->mailer->send($statement->email, $subject, $body);
    }
}
