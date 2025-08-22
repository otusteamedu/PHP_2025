<?php

declare(strict_types=1);

namespace Dinargab\Homework5\Service;

class EmailValidator
{

    /**
     * Проверяет email на корректность синтаксиса и наличие MX записей
     *
     * @param string $email Email адрес для проверки
     * @return bool true если email валидный, false в противном случае
     */
    public function verifyEmail(string $email): bool
    {
        return $this->checkSyntax($email) && $this->verifyMxRecords($email);
    }

    /**
     * Проверяет синтаксис email адреса
     *
     * @param string $email Email адрес для проверки
     * @return bool true если синтаксис корректный, false в противном случае
     */
    private function checkSyntax($email): bool
    {
        if (empty(trim($email))) {
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }

    /**
     * Проверяет наличие MX записей для домена email адреса
     *
     * @param string $email Email адрес для проверки MX записей
     * @return bool true если MX записи существуют, false в противном случае
     */
    private function verifyMxRecords(string $email): bool
    {
        $emailParts = explode("@", $email);
        $domain = $emailParts[1];
        return checkdnsrr($domain, "MX");
    }


    /**
     * Проверяет массив email адресов на валидность
     * 
     * @param array $emailsArray Массив email адресов для проверки
     * @return array<string, bool> Ассоциативный массив email => результат проверки
     */
    public function verifyEmails(array $emailsArray): array
    {

        if (empty($emailsArray)) {
            return [];
        }
        $returnArray = [];
        foreach ($emailsArray as $email) {
            $returnArray[$email] = $this->verifyEmail($email);
        }

        return $returnArray;
    }
}
