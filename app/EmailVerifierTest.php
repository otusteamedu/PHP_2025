<?php

namespace app;

class EmailVerifierTest
{
    /**
     * @return array
     */
    public function runTests(): array
    {
        $verifier = new EmailVerifier();

        $emailsToTest = [
            'test@example.com',
            'invalid-email',
            'user@domain.com',
            'fake@random.com',
            'user@invalid_domain',
        ];

        return $verifier->verifyEmails($emailsToTest);
    }
}