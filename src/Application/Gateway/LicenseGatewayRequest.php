<?php
declare(strict_types=1);

namespace Application\Gateway;

use DateTime;

class LicenseGatewayRequest
{
    public function __construct(
        public readonly int $userId,
        public readonly int $serviceId,
        public readonly DateTime $createDate,
        public readonly int $period
    )
    {
    }
}