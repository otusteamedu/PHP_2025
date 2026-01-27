<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Domain\ValueObject;

use InvalidArgumentException;

class Sku
{
    private string $sku;

    public function __construct(string $sku)
    {
        if ( ! $this->isValid($sku)) {
            throw new InvalidArgumentException("Sku '$sku' is not valid");
        }
        $this->sku = $sku;
    }

    private function isValid(string $sku)
    {
        return preg_match('/^\d{3}-\d{3}$/', $sku) === 1;
    }

    public function getValue(): string
    {
        return $this->sku;
    }
}