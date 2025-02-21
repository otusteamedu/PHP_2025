<?php

namespace App\Validators;

/**
 * Class BracketsValidator
 * @package App\Validators
 */
class BracketsValidator
{
    const OPENING_BRACKET_SYMBOL = '(';
    const CLOSING_BRACKET_SYMBOL = ')';

    /**
     * @param string $string
     * @return void
     * @throws ValidationException
     */
    public function validate(string $string): void
    {
        if (empty($string)) {
            throw new ValidationException('String must not be empty.');
        }

        $openingBracketsNum = 0;
        foreach (str_split($string) as $char) {
            if ($char == self::OPENING_BRACKET_SYMBOL) {
                $openingBracketsNum++;
            } elseif ($char == self::CLOSING_BRACKET_SYMBOL) {
                if ($openingBracketsNum <= 0) {
                    throw new ValidationException('Incorrect string.');
                }

                $openingBracketsNum--;
            } else {
                throw new ValidationException("Incorrect symbol '$char' in the string.");
            }
        }

        if ($openingBracketsNum != 0) {
            throw new ValidationException('Incorrect string.');
        }
    }
}
