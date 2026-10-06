<?php

declare(strict_types=1);

namespace App\Domain\BankReport\ValueObject;

use App\Domain\BankReport\Exception\EmailException;

readonly class Email
{
    public string $value;

    /**
     * @throws EmailException
     */
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
}
