<?php

namespace app;

class EmailVerifierTest
{
    /**
     * @return string
     */
    public function runTests(): string
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