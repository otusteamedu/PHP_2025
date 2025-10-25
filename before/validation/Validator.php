<?php 

namespace App\validation;

class Validator {
    public function isValid(string $string): bool {
        return $this->isValidEmailFormat($string) && $this->hasValidMxRecord($string);
    }

    private function isValidEmailFormat(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function hasValidMxRecord(string $email): bool {
        $domain = explode('@', $email)[1];  // Получаем домен после @
        return checkdnsrr($domain, 'MX'); //проверяем MX c помощью метода checkdnsrr
    }
}