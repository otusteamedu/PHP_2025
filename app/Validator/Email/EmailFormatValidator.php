<?php

declare(strict_types=1);

namespace App\Validator\Email;

class EmailFormatValidator
{
    private const string EMAIL_REGEX = '/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/';

    /**
     * Проверяет формат e‑mail
     */
    public function isValid(string $email): bool
    {
        return (bool) preg_match(self::EMAIL_REGEX, $email);
    }
}
