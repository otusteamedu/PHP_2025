<?php

namespace Crowley\Hw\Infrastructure\Persistence;

use Crowley\Hw\Domain\Repositories\EmailRepositoryInterface;

class EmailsRepository implements EmailRepositoryInterface
{

    public function checkMxRecords(array $emails): array {

        $mx_records = [];

        $result = [];

        foreach ($emails as $email) {

            // Проверяем синтаксис email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $result[$email] = '❌ Невалидный формат';
                continue;
            }

            // Берём домен после @
            $domain = substr(strrchr($email, "@"), 1);

            // Проверяем MX-записи
            if (getmxrr($domain, $mx_records)) {
                $result[$email] = '✅ Домен принимает почту';
            } else {
                $result[$email] = '❌ Нет MX-записей';
            }
        }

        return $result;

    }

}