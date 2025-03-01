<?php
declare(strict_types=1);

namespace App\Validation;

class Validation
{
    public static function isValidBrackets(string $string): bool
    {
        preg_match("/^((?>[^()]|\((?1)\))*)$/m", $string, $matches);

        return empty($matches);
    }

    public static function isEmptyString(string $string): bool
    {
        return empty($string);
    }
}