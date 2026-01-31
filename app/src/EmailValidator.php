<?php
namespace Pryaniki\App;

use Pryaniki\App\Exceptions;
use Pryaniki\App\Exceptions\EmptyFieldException;
use Pryaniki\App\Exceptions\NotValidException;

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
            echo $e->getMessage() . PHP_EOL;
        }

        return $isValid;
    }

    /**
     * @throws NotValidException
     * @throws EmptyFieldException
     */
    private static function checkEmail(string $email): void
    {
        if ($email === '') {
            throw new Exceptions\EmptyFieldException('email');
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new Exceptions\NotValidException('email');
        }
    }
    private static function checkDns(string $email): void
    {

    }
}