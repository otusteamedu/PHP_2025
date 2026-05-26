<?php

declare(strict_types=1);

namespace Pryaniki\Tests\Domain\ValueObjects\Email\Rules;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Pryaniki\App\Domain\ValueObjects\Email\Rules\EmailFormatRule;


final class EmailFormatRuleTest extends TestCase
{

    #[DataProvider('validEmailDataProvider')]
    public function testValidEmail(string $email): void
    {
        $rule = new EmailFormatRule();

        $result = $rule->validate($email);

        self::assertTrue(
            $result->isValid(),
            sprintf('Email "%s" should be valid', $email),
        );

        self::assertEmpty($result->getErrors());
    }

    public static function validEmailDataProvider(): array
    {
       return [
            'regular email' => [
                'test@gmail.com',
            ],

            'uppercase email' => [
                'USER@gmail.com',
            ],

            'email with dots' => [
                'user.name@gmail.com',
            ],

            'email with dash' => [
                'user-name@gmail.com',
            ],

            'email with underscore' => [
                'user_name@gmail.com',
            ],

            'email with plus alias' => [
                'user+tag@gmail.com',
            ],

            'subdomain email' => [
                'user@mail.google.com',
            ],

            'short email' => [
                'a@b.cd',
            ],
        ];
    }

    #[DataProvider('invalidEmailDataProvider')]
    public function testInvalidEmail(string $email): void
    {
        $rule = new EmailFormatRule();

        $result = $rule->validate($email);

        self::assertFalse(
            $result->isValid(),
            sprintf('Email "%s" should be invalid', $email),
        );

        self::assertNotEmpty($result->getErrors());

    }

    public static function invalidEmailDataProvider(): array
    {
        return [
            'empty string' => [
                '',
            ],

            'missing domain' => [
                'test@',
            ],

            'missing local part' => [
                '@gmail.com',
            ],

            'double at sign' => [
                'test@@gmail.com',
            ],

            'spaces inside email' => [
                'test test@gmail.com',
            ],

            'missing top level domain' => [
                'test@gmail',
            ],

            'plain text' => [
                'not-an-email',
            ],

            'double dots' => [
                'test..test@gmail.com',
            ],
        ];
    }
}