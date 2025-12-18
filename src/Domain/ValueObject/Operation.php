<?php

namespace App\Domain\ValueObject;

use App\Application\DTO\AllowedOperations;

final class Operation
{
    private AllowedOperations $allowedOperations;
    private string $operation;
    public function __construct(string $operation, AllowedOperations $allowedOperations)
    {
        $this->allowedOperations = $allowedOperations;
        $this->setOperation($operation);
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    private function setOperation(string $operation): void
    {
        if (!in_array($operation, $this->allowedOperations->getAllowedOperations())) {
            throw new \InvalidArgumentException('This operation is not allowed.');
        }

        $this->operation = $operation;
    }
}
