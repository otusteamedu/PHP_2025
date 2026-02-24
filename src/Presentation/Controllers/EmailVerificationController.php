<?php

namespace EmailsVerifier\Presentation\Controllers;

use EmailsVerifier\Application\Interfaces\VerifyEmailsUseCaseInterface;
use EmailsVerifier\Presentation\Services\VerificationResultProcessor;

readonly class EmailVerificationController
{
    public function __construct(
        private VerifyEmailsUseCaseInterface $verifyEmailsUseCase
    ) {}

    public function verifyEmails(array $emailAddresses): array
    {
        $results = $this->verifyEmailsUseCase->execute($emailAddresses);

        return VerificationResultProcessor::processResults($results);
    }
}
