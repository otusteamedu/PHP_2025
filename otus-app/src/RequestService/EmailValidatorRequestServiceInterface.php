<?php

declare(strict_types=1);

namespace App\RequestService;

interface EmailValidatorRequestServiceInterface
{
    public function isEmailConfirmationSent(string $email): bool;
}
