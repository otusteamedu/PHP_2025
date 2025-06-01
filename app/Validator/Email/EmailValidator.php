<?php

declare(strict_types=1);

namespace App\Validator\Email;

class EmailValidator implements EmailValidatorInterface
{
    private EmailFormatValidator $formatValidator;
    private DnsMxValidator $dnsValidator;

    public function __construct()
    {
        $this->formatValidator = new EmailFormatValidator();
        $this->dnsValidator    = new DnsMxValidator();
    }

    /**
     * {@inheritdoc}
     */
    public function verify(string $email): bool
    {
        $email = trim($email);

        if ($email === '') {
            return false;
        }

        if (!$this->formatValidator->isValid($email)) {
            return false;
        }

        return $this->dnsValidator->isValid($email);
    }

    /**
     * {@inheritdoc}
     */
    public function verifyList(array $emails): array
    {
        $result = [];

        foreach ($emails as $raw) {
            $key = trim((string)$raw);
            $result[$key] = $this->verify($key);
        }

        return $result;
    }
}

