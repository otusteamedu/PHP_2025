<?php

declare(strict_types=1);

namespace App\Domain\Cooking\Exception;

use DomainException;

class ProductIsBadException extends DomainException
{
    public function __construct(private readonly string $name)
    {
        parent::__construct(
            sprintf("%s не соответствует стандарту. Утилизируем.\n", $this->name)
        );
    }
}
