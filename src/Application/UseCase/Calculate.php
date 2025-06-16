<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\ValueObject\Number;
use App\Domain\ValueObject\Operation;

final class Calculate
{
    private string $operation;
    private int $number1;
    private int $number2;

    public function __construct(Operation $operation, Number $number1, Number $number2)
    {
        $this->operation = $operation->getOperation();
        $this->number1 = $number1->getNumber();
        $this->number2 = $number2->getNumber();
    }

    public function getResult(): int
    {
        return match ($this->operation) {
            'plus' => $this->plus(),
            'minus' => $this->minus(),
            'multiply' => $this->multiply(),
            'divide' => $this->divide(),
        };
    }

    private function plus(): int
    {
        return $this->number1 + $this->number2;
    }

    private function minus(): int
    {
        return $this->number1 - $this->number2;
    }

    private function multiply(): int
    {
        return $this->number1 * $this->number2;
    }

    private function divide(): int
    {
        if ($this->number2 === 0) {
            return 0;
        }

        return $this->number1 / $this->number2;
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    public function getNumber1(): int
    {
        return $this->number1;
    }

    public function getNumber2(): int
    {
        return $this->number2;
    }
}
