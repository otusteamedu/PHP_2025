<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Validator;

use App\Infrastructure\Http\Exception\InvalidArgumentException;

final readonly class TaskNumberValidator
{
    /**
     * @throws InvalidArgumentException
     */
    public static function validate(string $value): int
    {
        if (!ctype_digit($value)) {
            throw new InvalidArgumentException(
                'Task number must be a positive integer.'
            );
        }

        $number = (int)$value;

        if ($number <= 0) {
            throw new InvalidArgumentException(
                'Task number must be greater than zero.'
            );
        }

        return $number;
    }
}
