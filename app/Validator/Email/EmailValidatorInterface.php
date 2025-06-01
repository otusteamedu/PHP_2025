<?php

declare(strict_types=1);

namespace App\Validator\Email;

interface EmailValidatorInterface
{
    /**
     * Проверяет единичный e‑mail
     */
    public function verify(string $email): bool;

    /**
     * Проверяет несколько e‑mail
     */
    public function verifyList(array $emails): array;
}
