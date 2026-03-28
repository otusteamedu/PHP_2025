<?php

namespace Hafiz\Php2025\Service;

use Hafiz\Php2025\Validator\EmailValidator;
use Hafiz\Php2025\Validator\MXValidator;

class EmailVerificationService
{
    public function verifyEmails(array $emails): array
    {
        $results = [];
        foreach ($emails as $email) {
            if (!EmailValidator::isValidFormat($email)) {
                $results[$email] = "Invalid (Incorrect format)";
                continue;
            }

            [$user, $domain] = explode('@', $email);
            if (!MXValidator::hasMXRecord($domain)) {
                $results[$email] = "Invalid (No MX record)";
                continue;
            }

            $results[$email] = "Valid";
        }
        return $results;
    }
}
