<?php

declare(strict_types=1);

namespace App\Domain\Product\Exception;

use DomainException;

class InvalidProductException extends DomainException
{
    public function __construct(private readonly string $name)
    {
        parent::__construct(
            sprintf('Неизвестный продукт: %s', $this->name)
        );
    }
}
