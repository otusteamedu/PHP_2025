<?php

declare(strict_types=1);

namespace App;

require __DIR__ . '/Exceptions/ValidationException.php';

use App\Exceptions\ValidationException;

class Validator
{
    /**
     * @throws ValidationException
     */
    public function validateParentheses(string $string): void
    {
        $openCount = 0;

        foreach (str_split($string) as $char) {
            if ($char === '(') {
                $openCount++;
            } elseif ($char === ')') {
                $openCount--;
            }

            if ($openCount < 0) {
                throw new ValidationException("Unbalanced parentheses!");
            }
        }

        if ($openCount !== 0) {
            throw new ValidationException( "Unbalanced parentheses!");
        }
    }

    /**
     * @throws ValidationException
     */
    public function validateStringLength(mixed $string): void
    {
        if (!is_string($string)) {
            throw new ValidationException("Is not a string!");
        }
        if (empty($string)) {
            throw new ValidationException("String is empty!");
        }
    }
}