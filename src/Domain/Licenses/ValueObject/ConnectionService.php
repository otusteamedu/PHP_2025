<?php
declare(strict_types=1);

namespace Domain\Licenses\ValueObject;

use Domain\Catalog\Services\Entity\Service;

class ConnectionService
{
    private Service $value;

    public function __construct(Service $value)
    {
        $this->assertValidName($value);
        $this->value = $value;
    }

    public function getValue(): Service
    {
        return $this->value;
    }

    private function assertValidName(Service $value): void
    {
        if ($value->getId() === null) {
            throw new \InvalidArgumentException('Error Licenses ConnectionService');
        }
    }
}