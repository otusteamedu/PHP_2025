<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\ValueObject\Recipient;

use App\Domain\OrderNotification\Exception\Input\EmailException;

readonly class Email implements RecipientInterface
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw EmailException::notEmpty();
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw EmailException::invalid($value);
        }

        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
}
