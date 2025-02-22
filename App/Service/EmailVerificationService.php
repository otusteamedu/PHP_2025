<?php

declare(strict_types=1);

namespace App\Service;

use App\Validator\EmailValidator;
use App\Validator\MXValidator;

class EmailVerificationService
{
    public function __invoke(array $emails): array
    {
        $results = [];

        if (empty($emails)) {
            return $results;
        }

        $isValidEmail = new EmailValidator();
        $isHaveMXRecord = new MXValidator();

        foreach ($emails as $email) {
            [, $domain] = \explode('@', $email);

            $results[$email] = [
                'isValidByChars' => $isValidEmail($email) ? "Валидный" : "Не валидный",
                'isValidByMX' => $isHaveMXRecord($domain) ? "Есть MX запись" : "Нет MX записи",
            ];
        }

        return $results;
    }
}
