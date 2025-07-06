<?php

namespace App\Service;

use App\Exception\InvalidEmailException;
use App\Exception\NoMxRecordException;

class EmailValidatorService
{
    /**
     * @param array $emails
     * @return true
     */
    public static function validate(array $emails): true
    {
        foreach ($emails as $email) {
            if (!preg_match('/^[\w\d._%+-]{2,}@[\w\d.-]+\.[\w]{2,}$/', $email)) {
                throw new InvalidEmailException($email);
            }
            $emailPart = explode('@', $email);

            $domain = $emailPart[1];
            if (!checkdnsrr($domain, 'MX')) {
                throw new NoMxRecordException($domain);
            }
        }

        return true;

    }

}