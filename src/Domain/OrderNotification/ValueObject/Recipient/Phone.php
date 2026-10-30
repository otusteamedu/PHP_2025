<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\ValueObject\Recipient;

use App\Domain\OrderNotification\Exception\Input\PhoneException;

readonly class Phone implements RecipientInterface
{
    private const string RF_PHONE_PATTERN = '/^\+7\d{10}$/';

    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw PhoneException::notEmpty();
        }

        if (preg_match(self::RF_PHONE_PATTERN, $value) !== 1) {
            throw PhoneException::invalid($value);
        }

        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
}
