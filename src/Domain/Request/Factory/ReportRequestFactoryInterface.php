<?php
declare(strict_types=1);

namespace Dinargab\Homework19\Domain\Request\Factory;

use Dinargab\Homework19\Domain\Request\Entity\ReportRequest;

interface ReportRequestFactoryInterface
{
    public function create(string $startDate, string $endDate, string $notificationEmail): ReportRequest;
}