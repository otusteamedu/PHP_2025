<?php

namespace App\Validation;

use App\Error\ValidationException;

class Validation
{

    /**
     * @return array|null
     * @throws ValidationException
     */
    public static function validate(): ?array
    {
        $input = $_POST;
        $string = $input['string'];

        if (empty($string)) {
            throw new ValidationException("Key 'string' is empty");
        }

        if (preg_match('/\s/', $string)) {
            throw new ValidationException("Key 'string' have spaces");
        }

        $countOpenBrackets = 0;

        foreach (str_split($string) as $value) {
            if ($value == '(') {
                $countOpenBrackets++;
            } else if ($value == ')') {
                $countOpenBrackets--;
                if ($countOpenBrackets < 0) {
                    throw new ValidationException("The brackets have the wrong structure");
                }
            }
        }

        if ($countOpenBrackets !== 0) {
            throw new ValidationException("The brackets have the wrong structure");
        }

        return $input;
    }
}