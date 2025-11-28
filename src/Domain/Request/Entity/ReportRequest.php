<?php
declare(strict_types=1);

namespace Dinargab\Homework19\Domain\Request\Entity;

use Dinargab\Homework19\Domain\ValueObject\Email;
use Dinargab\Homework19\Infrastructure\Service\Notification\EmailNotification;

class ReportRequest
{
    private int $id;
    public function __construct(
        private readonly \DateTimeImmutable $startDate,
        private readonly \DateTimeImmutable $endDate,
        private readonly Email $notificationEmail,
    ) {
        $this->id = rand(1, 1_000_000);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEndDate(): \DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getNotificationEmail(): Email
    {
        return $this->notificationEmail;
    }
}