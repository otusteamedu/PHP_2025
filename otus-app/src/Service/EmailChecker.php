<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\EmailInvalidException;
use Exception;
use Throwable;

class EmailChecker
{
    /**
     * @throws Exception
     */
    public function checkIsValidEmailList(array $emailList): void
    {
        if (empty($emailList)) {
            throw new EmailInvalidException();
        }

        foreach ($emailList as $email) {
            if (!is_string($email)) {
                throw new EmailInvalidException();
            }

            if ($this->isValidEmail($email) === false) {
                throw new EmailInvalidException();
            }
        }
    }

    /**
     * @throws Exception
     */
    public function isValidEmail(string $email): bool
    {
        if (empty(trim($email))) {
            return false;
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        $domain = explode('@', $email)[1];

        if (checkdnsrr($domain) === false) {
            return false;
        }

        if ($this->isEmailConfirmationSent() === false) {
            return false;
        }

        return true;
    }

    private function isEmailConfirmationSent(): bool
    {
        try {
            //some logics
        } catch (Throwable) {
            return false;
        }

        return true;
    }
}
