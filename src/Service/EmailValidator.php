<?php

declare(strict_types=1);

namespace Dinargab\Homework5\Service;

use Dinargab\Homework5\Result\ValidationResult;
use Dinargab\Homework5\Result\ResultInterface;

class EmailValidator
{

    /**
     * Согласно RFC 5321, RFC 3696 и https://stackoverflow.com/a/574698
     */
    private const EMAIL_MAX_LENGTH = 254;

    /**
     * Проверяет email на корректность формата, длину и наличие MX записей
     *
     * @param string $email Email адрес для проверки
     * @return ValidationResult Результат проверки email
     */
    public function verifyEmail(string $email): ResultInterface
    {
        $inputEmail = trim($email);
        if (empty($inputEmail)) {
            return new ValidationResult($email, false, "Email is empty");
        }

        //Не обязательно, но зато ошибка будет читаемая
        if (!$this->checkLength($inputEmail)) {
            return new ValidationResult($email, false, "Email is longer than " . self::EMAIL_MAX_LENGTH . " characters");
        }

        if (filter_var($inputEmail, FILTER_VALIDATE_EMAIL) === false) {
            return new ValidationResult($email, false, "Invalid email format");
        }

        if (!$this->verifyMxRecords($email)) {
            return new ValidationResult($email, false, 'No MX records found for email domain');
        }

        return new ValidationResult($email, true);
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
     * Проверяет длину строки с Email адресом
     * 
     * @param string $email Email адрес для проверки длины
     * @return bool true если длина не превышает макс длину email 
     */

    private function checkLength(string $email): bool
    {
        return strlen($email) <= self::EMAIL_MAX_LENGTH;
    }


    /**
     * Проверяет массив email адресов на валидность
     * 
     * @param array $emailsArray Массив email адресов для проверки
     * @return array<ValidationResult> Массив результатов проверки email
     */
    public function verifyEmails(array $emailsArray): array
    {

        if (empty($emailsArray)) {
            return [];
        }

        $returnArray = [];
        foreach ($emailsArray as $email) {
            $returnArray[] = $this->verifyEmail($email);
        }

        return $returnArray;
    }
}
