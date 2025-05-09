<?php

namespace Domain\ValueObject;

class Product
{
    private array $value;

    public function __construct(array $value)
    {
        $this->assertValidName($value);
        $this->value = $value;
    }

    public function getValue(): array
    {
        return $this->value;
    }

    private function assertValidName(array $value): void
    {
        if(!$value){
            throw new \InvalidArgumentException("Заказ не сущесвует!");
        }
    }
}