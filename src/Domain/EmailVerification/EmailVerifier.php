<?php

declare(strict_types=1);

namespace App\Domain\EmailVerification;

use App\Domain\Shared\Validator\EmailValidator;

class EmailVerifier
{
    public function __construct(
        private readonly EmailValidator $emailValidator,
    ) {
    }

    /**
     * @return string[]
     */
    public function getValidEmails(array $emails): array
    {
        $validEmails = array_filter($emails, fn(string $email) => $this->emailValidator->isValid($email));

        return array_values($validEmails);
    }
}
