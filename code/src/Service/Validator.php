<?php

declare(strict_types=1);

namespace Dinargab\Homework4\Service;

use InvalidArgumentException;

class Validator
{

    private const string OPENING_BRACKET = "(";
    private const string CLOSING_BRACKET = ")";

    public function validateString(string $string): bool
    {
        $inputString = trim($string);
        if (empty(trim($inputString))) {
            throw new InvalidArgumentException("String is empty");
        }

        $bracketSum = 0;
        foreach (mb_str_split($inputString) as $char) {

            if ($char === self::OPENING_BRACKET) {
                $bracketSum++;
            } elseif ($char === self::CLOSING_BRACKET) {
                $bracketSum--;
            } else {
                throw new InvalidArgumentException("Invalid symbol in string");
            }

            if ($bracketSum < 0) {
                return false;
            }
        }

        return $bracketSum === 0;
    }
}