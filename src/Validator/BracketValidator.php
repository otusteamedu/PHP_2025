<?php
declare(strict_types=1);

namespace App\Validator;

use App\Exception\ValidationException;

class BracketValidator
{
    /**
     * @param string $string
     * @return void
     * @throws ValidationException
     */
    public function validate(string $string): void
    {
        if ($string === '') {
            throw new ValidationException('Empty string');
        }

        $counter = 0;
        $length = strlen($string);

        for ($i = 0; $i < $length; $i++) {
            $currentChar = $string[$i];

            match ($currentChar) {
                '(' => $counter++,
                ')' => $counter--,
                default => throw new ValidationException('Invalid character: ' . $currentChar)
            };

            if ($counter < 0) {
                throw new ValidationException('Incorrect order of brackets');
            }
        }

        if ($counter !== 0) {
            throw new ValidationException('Unbalanced brackets');
        }
    }
}
