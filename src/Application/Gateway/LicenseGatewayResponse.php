<?php
declare(strict_types=1);

namespace Application\Gateway;

class LicenseGatewayResponse
{
    public function __construct(
        public readonly string $serialNumber,
    )
    {
    }
}