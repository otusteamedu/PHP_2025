<?php

namespace App\Validator;

use App\Exception\EmptyValueException;
use App\Exception\InvalidCharacterException;
use App\Exception\TypeException;

class Validator
{
    public function validate($value): bool
    {
        if (!is_string($value)) {
            throw new TypeException(humanReadableException: 'Неизвестный формат данных');
        }

        if (empty($value)) {
            throw new EmptyValueException(humanReadableException: 'Передано пустое значение');
        }

        if (preg_match('/[^()]/', $value) !== 0) {
            throw new InvalidCharacterException(humanReadableException: 'Недопустимые символы в строке');
        }

        $count = 0;

        for ($i = 0; $i < strlen($value); $i++) {
            if ($value[$i] === '(') {
                $count++;
            } elseif ($value[$i] === ')') {
                $count--;
                if ($count < 0) {
                    return false;
                }
            }
        }

        return $count === 0;
    }
}