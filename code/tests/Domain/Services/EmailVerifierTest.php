<?php

declare(strict_types=1);

namespace Alisaselezneva\Code\Tests\Domain\Services;

use Alisaselezneva\Code\Domain\Services\EmailVerifier;
use Alisaselezneva\Code\Domain\Services\VerificationResult;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class EmailVerifierTest extends TestCase
{
    public function testCheckFormatValid(): void
    {
        $validEmails = [
            'user@example.com',
            'john.doe@example.org',
            'user+tag@example.co.uk',
            'name_surname@sub.domain.net',
            'test123@my-domain.io',
        ];

        $verifier = new EmailVerifier();
        $method = new ReflectionMethod(EmailVerifier::class, 'checkFormat');

        foreach ($validEmails as $email) {
            $this->assertTrue($method->invoke($verifier, $email), "Expected valid email: {$email}");
        }
    }

    public function testCheckFormatInvalid(): void
    {
        $invalidEmails = [
            'wrong-email',
            'user..name@example.com',
            '.user@example.com',
            'user.@example.com',
            'user@example',
            'user@.example.com',
            'user@example.com.',
            'user@example..com',
            'user@-example.com',
            'user@exa_mple.com',
            'user@example.123',
            'user name@example.com',
            str_repeat('a', 64) . '@' . str_repeat('b', 186) . '.com',
        ];

        $verifier = new EmailVerifier();
        $method = new ReflectionMethod(EmailVerifier::class, 'checkFormat');

        foreach ($invalidEmails as $email) {
            $this->assertFalse($method->invoke($verifier, $email), "Expected invalid email: {$email}");
        }
    }

    public function testIsValidTldValid(): void
    {
        $validDomains = [
            'example.com',
            'mail.example.123org',
            'service.example.co.uk',
        ];

        $verifier = new EmailVerifier();
        $method = new ReflectionMethod(EmailVerifier::class, 'isValidTld');

        foreach ($validDomains as $domain) {
            $this->assertTrue($method->invoke($verifier, $domain), "Expected valid TLD in domain: {$domain}");
        }
    }

    public function testIsValidTldInvalid(): void
    {
        $invalidDomains = [
            'example.123',
            'example.---',
        ];

        $verifier = new EmailVerifier();
        $method = new ReflectionMethod(EmailVerifier::class, 'isValidTld');

        foreach ($invalidDomains as $domain) {
            $this->assertFalse($method->invoke($verifier, $domain), "Expected invalid TLD in domain: {$domain}");
        }
    }

    public function testCheckMxRecordsValid(): void
    {
        $validEmails = [
            'user@gmail.com',
        ];

        $verifier = new EmailVerifier();
        $method = new ReflectionMethod(EmailVerifier::class, 'checkMxRecords');

        foreach ($validEmails as $email) {
            $this->assertTrue($method->invoke($verifier, $email), "Expected MX records for email: {$email}");
        }
    }

    public function testCheckMxRecordsInvalid(): void
    {
        $invalidEmails = [
            'user@invalid-domain-for-tests.local',
            'user@',
        ];

        $verifier = new EmailVerifier();
        $method = new ReflectionMethod(EmailVerifier::class, 'checkMxRecords');

        foreach ($invalidEmails as $email) {
            $this->assertFalse($method->invoke($verifier, $email), "Expected no MX records for email: {$email}");
        }
    }

    public function testVerifyValid(): void
    {
        $validEmail = 'user@gmail.com';

        $verifier = new EmailVerifier();

        $result = $verifier->verify($validEmail);

        $this->assertInstanceOf(VerificationResult::class, $result);
        $this->assertTrue($result->isValid());
        $this->assertSame(
            [
                'format' => true,
                'mx_records' => true,
            ],
            $result->getChecks()
        );
    }

    public function testVerifyInvalid(): void
    {
        $verifier = new EmailVerifier();

        $invalidCases = [
            'user@example' => [
                'format' => false,
                'mx_records' => false,
            ],
            'user@invalid-domain-for-tests.local' => [
                'format' => true,
                'mx_records' => false,
            ],
        ];

        foreach ($invalidCases as $email => $expectedChecks) {
            $result = $verifier->verify($email);

            $this->assertInstanceOf(VerificationResult::class, $result);
            $this->assertFalse($result->isValid());
            $this->assertSame($expectedChecks, $result->getChecks());
        }
    }
}
