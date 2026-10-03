<?php

declare(strict_types=1);

namespace App\Domain\BankReport\ValueObject;

use App\Domain\BankReport\Exception\ClientNameException;

readonly class ClientName
{
    public const int MIN_LEN = 5;
    public const int MAX_LEN = 50;

    public string $value;

    /**
     * @throws ClientNameException
     */
    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw ClientNameException::notEmpty();
        }

        if (mb_strlen($value) < self::MIN_LEN) {
            throw ClientNameException::tooShort(self::MIN_LEN);
        }

        if (mb_strlen($value) > self::MAX_LEN) {
            throw ClientNameException::tooLong(self::MAX_LEN);
        }

        $this->value = $value;
    }
}
