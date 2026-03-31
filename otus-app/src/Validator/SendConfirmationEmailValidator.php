<?php

declare(strict_types=1);

namespace App\Validator;

use App\RequestService\EmailValidatorRequestServiceInterface;

class SendConfirmationEmailValidator implements EmailValidatorInterface
{
    public function __construct(
        private EmailValidatorRequestServiceInterface $requestService,
    ) {
    }

    public function isValidEmail(string $email): bool
    {
        return $this->requestService->isEmailConfirmationSent($email) !== false;
    }
}
