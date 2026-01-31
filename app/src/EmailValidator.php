<?php

class EmailValidator
{
    public static function validate(string $email): bool
    {
        $isValid = false;

        try {
            self::checkEmail($email);
            self::checkDns($email);
            $isValid = true;
        } catch (\Exception $e) {

        }

        return $isValid;
    }

    private static function checkEmail(string $email): void
    {

    }
    private static function checkDns(string $email): void
    {

    }
}