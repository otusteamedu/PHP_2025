<?php
declare(strict_types=1);

namespace Domain\Licenses\Factory;

use DateTime;
use Domain\Licenses\Entity\License;

interface LicenseFactoryInterface
{
    public function create(int $userId, int $serviceId, DateTime $createDate, int $period, string $serialNumber): License;
}