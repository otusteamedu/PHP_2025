<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\DTO\AllowedOperations;
use App\Application\UseCase\Calculate;
use App\Domain\ValueObject\Number;
use App\Domain\ValueObject\Operation;

final class CalculateBuilder
{
    private Operation $operation;
    private Number $number1;
    private Number $number2;

    public function setOperation(string $operation = 'plus'): self
    {
        $this->operation = new Operation($operation, new AllowedOperations());

        return $this;
    }

    public function setNumber1(string $number1): self
    {
        $this->number1 = new Number($number1);

        return $this;
    }

    public function setNumber2(string $number2): self
    {
        $this->number2 = new Number($number2);

        return $this;
    }

    public function getInstance(): Calculate
    {
        return new Calculate($this->operation, $this->number1, $this->number2);
    }
}
