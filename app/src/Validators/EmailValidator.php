<?php

namespace App\Validators;

use DomainException;

/**
 * Class EmailValidator
 * @package App\Validators
 */
class EmailValidator
{
    /**
     * @var bool whether validation process should take into account IDN (internationalized domain names).
     * Note that in order to use IDN validation you have to install and enable `intl` PHP extension,
     * otherwise an exception would be thrown.
     */
    private bool $enableIDN;

    /**
     * @param bool $enableIDN
     */
    public function __construct(bool $enableIDN = false)
    {
        $this->enableIDN = $enableIDN;

        if ($this->enableIDN && !function_exists('idn_to_ascii')) {
            throw new DomainException('In order to use IDN validation intl extension must be installed and enabled.');
        }
    }

    /**
     * @param string $email
     * @return bool
     */
    public function validateFormat(string $email): bool
    {
        if ($this->enableIDN) {
            $email = $this->normalizeEmail($email);
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function validateDnsMx(string $email): bool
    {
        if ($this->enableIDN) {
            $email = $this->normalizeEmail($email);
        }

        $domain = $this->getDomain($email);
        return $domain && checkdnsrr($domain);
    }

    /**
     * @param string $email
     * @return string
     */
    private function normalizeEmail(string $email): string
    {
        if (!str_contains($email, '@')) {
            return $this->idnToAscii($email);
        }

        [$local, $domain] = explode('@', $email);

        return implode('@', [
            $this->idnToAscii($local),
            $this->idnToAscii($domain)
        ]);
    }

    /**
     * @param string $idn
     * @return string|false
     */
    private function idnToAscii(string $idn): string|false
    {
        return idn_to_ascii($idn, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46);
    }

    /**
     * @param string $email
     * @return string
     */
    private function getDomain(string $email): string
    {
        $domain = substr(strrchr($email, "@"), 1);
        $urlData = parse_url('mailto://' . $domain);

        if (!$urlData) {
            return $domain;
        }

        $host = $urlData['host'] ?? '';
        $hostData = explode('.', $host);

        return implode('.', array_slice($hostData, -2, 2));
    }
}
