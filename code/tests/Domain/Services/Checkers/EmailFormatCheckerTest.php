<?php

declare(strict_types=1);

namespace Alisaselezneva\Code\Tests\Domain\Services\Checkers;

use Alisaselezneva\Code\Domain\Services\Checkers\EmailFormatChecker;
use PHPUnit\Framework\TestCase;

class EmailFormatCheckerTest extends TestCase
{
    public function testIsValidValid(): void
    {
        $validEmails = [
            'user@example.com',
            'john.doe@example.org',
            'user+tag@example.co.uk',
            'name_surname@sub.domain.net',
            'test123@my-domain.io',
        ];

        $checker = new EmailFormatChecker();

        foreach ($validEmails as $email) {
            $this->assertTrue($checker->isValid($email), "Expected valid email: {$email}");
        }
    }

    public function testIsValidInvalid(): void
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

        $checker = new EmailFormatChecker();

        foreach ($invalidEmails as $email) {
            $this->assertFalse($checker->isValid($email), "Expected invalid email: {$email}");
        }
    }
}
