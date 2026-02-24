<?php

namespace EmailsVerifier\Application\UseCases;

use EmailsVerifier\Domain\Email;
use EmailsVerifier\Domain\Interfaces\EmailValidationInterface;
use EmailsVerifier\Application\Interfaces\VerifyEmailsUseCaseInterface;
use EmailsVerifier\Application\DTO\VerificationResultDTO;

readonly class VerifyEmailsUseCase implements VerifyEmailsUseCaseInterface
{
    public function __construct(
        private EmailValidationInterface $emailValidator
    ) {}

    public function execute(array $emailAddresses): array
    {
        $results = [];

        foreach ($emailAddresses as $emailAddress) {
            $email = new Email($emailAddress);

            $errors = $this->emailValidator->validate($email);

            $isValid = empty($errors);

            $results[] = new VerificationResultDTO($email, $isValid, $errors);
        }

        return $results;
    }
}
