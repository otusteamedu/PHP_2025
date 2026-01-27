<?php

namespace App\Service;

class EmptyValidator implements ValidatorInterface
{
    public function validate(string $string): ValidationResult
    {
        if (strlen($string) == 0){
            return new ValidationResult("string пустой", self::BAD_REQUEST);
        }

        return new ValidationResult("", self::OK_REQUEST);
    }
}