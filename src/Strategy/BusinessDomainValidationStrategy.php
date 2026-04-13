<?php

declare(strict_types=1);

namespace App\Strategy;

final class BusinessDomainValidationStrategy implements EmailValidationStrategy
{
    /** @var string[] */
    private array $allowedDomains;

    /**
     * @param string[] $allowedDomains
     */
    public function __construct(array $allowedDomains)
    {
        $this->allowedDomains = array_map('strtolower', $allowedDomains);
    }

    public function isValid(string $email): bool
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        $domain = strtolower((string) substr(strrchr($email, '@') ?: '', 1));
        if ($domain === '') {
            return false;
        }

        return in_array($domain, $this->allowedDomains, true);
    }
}
