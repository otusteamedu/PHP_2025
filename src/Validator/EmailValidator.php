<?php
declare(strict_types=1);

namespace App\Validator;

class EmailValidator
{
    private const string EMAIL_REGEX = '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i';

    /**
     * @param string $email
     * @return bool
     */
    public function isValidFormat(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (!preg_match(self::EMAIL_REGEX, $email)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function hasMxRecord(string $email): bool
    {
        $domain = substr(strrchr($email, '@'), 1);
        return checkdnsrr($domain);
    }
}
