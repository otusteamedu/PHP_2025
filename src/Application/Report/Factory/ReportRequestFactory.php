<?php
declare(strict_types=1);

namespace Dinargab\Homework19\Application\Report\Factory;

use Dinargab\Homework19\Domain\Request\Entity\ReportRequest;
use Dinargab\Homework19\Domain\Request\Factory\ReportRequestFactoryInterface;
use Dinargab\Homework19\Domain\ValueObject\Email;

class ReportRequestFactory implements ReportRequestFactoryInterface
{

    public function create(string $startDate, string $endDate, string $notificationEmail): ReportRequest
    {
        return new ReportRequest(
            new \DateTimeImmutable($startDate),
            new \DateTimeImmutable($endDate),
            new Email($notificationEmail)
        );
    }
}