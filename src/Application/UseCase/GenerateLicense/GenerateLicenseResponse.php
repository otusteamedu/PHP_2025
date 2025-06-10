<?php
declare(strict_types=1);

namespace Application\UseCase\GenerateLicense;

class GenerateLicenseResponse
{
    public function __construct(
        public readonly int $id,
        public readonly string $serialNumber,
    )
    {
    }
}