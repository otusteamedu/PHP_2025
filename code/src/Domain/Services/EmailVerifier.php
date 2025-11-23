<?php

namespace Alisaselezneva\Code\Domain\Services;

class EmailVerifier
{
    public function verify(string $email): VerificationResult
    {
        $checks = [
            'format' => $this->checkFormat($email),
            'mx_records' => $this->checkMxRecords($email),
        ];

        $isValid = $checks['format'] && $checks['mx_records'];
        
        return new VerificationResult($isValid, $checks);
    }

    /*
    Адрес электронной почты состоит из двух частей, разделённых символом «@»: 

    Локальная часть — перед символом «@», идентифицирует пользователя в пределах одного почтового сервиса. Может содержать латинские буквы (a-z), цифры (0-9), а также некоторые специальные символы, такие как точки, дефисы и подчёркивания.
    Доменная часть — идёт после символа «@», определяет почтовый сервер, на котором зарегистрирован адрес. Состоит из нескольких сегментов, разделённых точками.
    
    Ограничения по длине:
    локальная часть — не более 64 символов;
    доменная часть — не более 255 символов (если рассматривается отдельно);
    общая длина адреса — не более 254 символов.
    
    Домены
    Доменная часть должна соответствовать стандартам DNS и содержать минимум одну точку, 
    разделяющую доменное имя и доменную зону.
    
    Допустимые символы:
    в локальной части 
        прописные и строчные латинские буквы от A до Z и от a до z
        цифры от 0 до 9
        печатные символы !#$%&'*+-/=?^_`{|}~
        точка ., при условии, что это не первый и не последний символ, а также при условии, что они не следуют друг за другом (например, John..Doe@example.com не допускается).;

    в доменной части
        Заглавные и строчные латинские буквы от A до Z и от a до z;
        Цифры 0 - 9 включительно, при условии, что доменные имена верхнего уровня не являются полностью цифровыми;
        Дефис -, если он не является первым или последним символом в слове.
    
    Точка не может стоять в начале или конце имени пользователя, 
    а также не может встречаться дважды подряд.
    */
    private function checkFormat(string $email): bool
    {
        // Регулярное выражение для проверки формата email
        $pattern = '/^[a-zA-Z0-9\.!#$%&\'\*+-\/=\?\^_\`{|}~]{0,64}@[a-zA-Z0-9\.\-]{0,255}$/';
        
        if (!preg_match($pattern, $email)) {
            return false;
        }

        // Дополнительные проверки
        $parts = explode('@', $email);
        $localPart = $parts[0];
        $domain = $parts[1] ?? '';

        // Проверка длины
        if (strlen($email) > 254) {
            return false;
        }

        // Проверка на две точки подряд 
        if (strpos($email, '..') !== false) {
            return false;
        }

        // Проверка, что локальная часть не начинается или не заканчивается на точку
        if (preg_match('/^[.]|[.]$/', $localPart)) {
            return false;
        }

        // Проверка, что домен содержит хотя бы одну точку
        if (strpos($domain, '.') === false) {
            return false;
        }

        // Проверка, что домен не начинается или не заканчивается на точку или дефис
        if (preg_match('/^[.-]|[.-]$/', $domain)) {
            return false;
        }

        // Проверка, что TLD не является полностью цифровым
        if (!$this->isValidTld($domain)) {
            return false;
        }

        return true;
    }

    private function isValidTld(string $domain): bool
    {
        // Извлекаем TLD (последнюю часть домена после последней точки)
        $tld = substr($domain, strrpos($domain, '.') + 1);
        
        // Проверяем, что TLD не состоит только из цифр
        if (preg_match('/^\d+$/', $tld)) {
            return false;
        }
        
        // Дополнительно: TLD должен содержать хотя бы одну букву
        if (!preg_match('/[a-zA-Z]/', $tld)) {
            return false;
        }
        
        return true;
    }

    private function checkMxRecords(string $email): bool
    {
        $domain = substr(strrchr($email, "@"), 1);

        if (empty($domain)) return false;

        return checkdnsrr($domain, 'MX');
    }

    public function massVerify(array $emails): array
    {
        $results = [];
        
        foreach ($emails as $email) {
            $results[] = $this->verify($email);
        }

        return $results;
    }
}