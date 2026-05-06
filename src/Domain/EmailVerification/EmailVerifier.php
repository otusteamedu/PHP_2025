<?php

declare(strict_types=1);

namespace App\Domain\EmailVerification;

use App\Domain\Shared\Validator\DnsMxRecordValidator;
use App\Domain\Shared\Validator\EmailFormatValidator;
use App\Domain\Shared\Validator\EmailValidator;

class EmailVerifier
{
    private readonly EmailValidator $emailValidator;

    public function __construct(
        private readonly array $emails,
    ) {
        $this->emailValidator = new EmailValidator(
            new EmailFormatValidator(),
            new DnsMxRecordValidator(),
        );
    }

    /**
     * @return string[]
     */
    public function getValidEmails(): array
    {
        $validEmails = array_filter($this->emails, fn(string $email) => $this->emailValidator->isValid($email));

        return array_values($validEmails);
    }
}
