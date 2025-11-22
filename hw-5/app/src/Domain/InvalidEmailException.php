<?php

declare(strict_types=1);

namespace App\Domain;

use DomainException;

final class InvalidEmailException extends DomainException
{
    public function __construct(private readonly string $value)
    {
        parent::__construct(sprintf('Invalid email format: %s', $this->value));
    }
}
