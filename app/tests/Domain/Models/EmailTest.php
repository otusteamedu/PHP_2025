<?php

declare(strict_types=1);

namespace Pryaniki\Tests\Domain\Models;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Pryaniki\App\Domain\Models\Email;

final class EmailTest extends TestCase
{
    #[DataProvider('domainProvider')]
    public function testExtractsDomain(
        string $email,
        string $expectedDomain,
    ): void {
        $emailObject = new Email($email);

        self::assertSame(
            $expectedDomain,
            $emailObject->getDomain(),
        );
    }

    public static function domainProvider(): array
    {
        return [
            'simple email' => [
                'test@gmail.com',
                'gmail.com',
            ],

            'subdomain email' => [
                'user@mail.google.com',
                'mail.google.com',
            ],

            'uppercase email' => [
                'USER@GMAIL.COM',
                'GMAIL.COM',
            ],

            'empty string' => [
                '',
                '',
            ],

            'missing @ symbol' => [
                'word',
                'word',
            ],
        ];
    }
}