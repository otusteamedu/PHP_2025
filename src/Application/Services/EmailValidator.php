<?php
declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Interfaces\EmailValidatorInterface;

class EmailValidator implements EmailValidatorInterface
{

    public const MESSAGE_VALIDATE_FORMAT = 'Invalid email format';
    public const MESSAGE_NO_MX_RECORDS = 'No MX records found for domain';

    private array $arMXCheckResult = [];

    public function validate(array $arEmails): array
    {
        $arResults = [];
        foreach ($arEmails as $email) {
            $arResults[$email] = $this->verifyEmail($email);
        }

        return $arResults;
    }

    private function verifyEmail(string $email): array
    {
        $arResult = [
            'is_valid' => false,
            'is_valid_format' => false,
            'is_valid_dns' => null,
            'errors' => []
        ];

        if (!$this->validateFormat($email)) {
            $arResult['errors'][] = self::MESSAGE_VALIDATE_FORMAT;
            return $arResult;
        }

        $arResult['is_valid_format'] = true;

        if (!$this->checkDnsMx($this->extractDomain($email))) {
            $arResult['errors'][] = self::MESSAGE_NO_MX_RECORDS;
            $arResult['is_valid_dns'] = false;

            return $arResult;
        }

        $arResult['is_valid_dns'] = true;
        $arResult['is_valid'] = true;

        return $arResult;
    }

    private function validateFormat(string $email): bool
    {
        $pattern = '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/';

        return (bool)preg_match($pattern, $email);
    }

    private function extractDomain(string $email): string
    {
        return substr($email, strpos($email, '@') + 1);
    }

    private function checkDnsMx(string $domain): bool
    {
        if (empty($domain)) {
            return false;
        }

        if (!array_key_exists($domain, $this->arMXCheckResult)) {
            $this->arMXCheckResult[$domain] = checkdnsrr($domain);
        }

        return checkdnsrr($domain);
    }
}