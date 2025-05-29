<?php
declare(strict_types=1);

namespace Aovchinnikova\Hw15\Service;

use Aovchinnikova\Hw15\Model\ValidationResult;

class EmailValidationService
{
    public function validate(string $email): ValidationResult
    {
        $isValidFormat = $this->isValidFormat($email);
        $hasValidDNS = $this->hasValidDNS($email);

        return new ValidationResult($email, $isValidFormat, $hasValidDNS);
    }

    private function isValidFormat(string $email): bool
    {
        $pattern = '/^[\w.%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}$/';

        return preg_match($pattern, $email) === 1;
    }

    private function hasValidDNS(string $email): bool
    {    
        $domainPart = strrchr($email, "@");
        if ($domainPart === false) {
            return false;
        }
    
        $domain = substr($domainPart, 1);
        return !empty($domain) && checkdnsrr($domain, "MX");
    }
}
