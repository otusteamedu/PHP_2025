<?php

namespace Tests\Unit;

use App\Base\ServiceContainer;
use App\Services\EmailService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EmailServiceTest extends TestCase
{
    private EmailService $emailService;

    public static function emailDataProvider(): iterable
    {
        // Valid simple email with known domain having MX
        yield ['user@gmail.com', true];
        yield ['test@aol.com', true];

        // Valid with special characters (allowed in local part)
        yield ['user+tag@example.com', true];  // example.com has A record, so passes fallback
        yield ['!#$%&\'*+-/=?^_`{|}~@example.com', true];  // All allowed special chars
        yield ['"quoted local part"@example.com', false];  // Quoted string for local part

        // Valid syntax but non-existent domain (assuming no MX or A/AAAA)
        yield ['user@nonexistentdomain123.com', false];
        yield ['valid@blahblahnonexist.blah', false];

        // Invalid syntax
        yield ['invalid', false];
        yield ['user@', false];
        yield ['@domain.com', false];
        yield ['user@domain with space.com', false];
        yield ['user@domain..com', false];  // Consecutive dots
        yield ['user.@domain.com', false];  // Dot at end of local
        yield ['.user@domain.com', false];  // Dot at start

        // More specials
        yield ['ab.cd-ef_gh+ij@example.com', true];
        // Assuming domain.com has records (note: in reality, sub.domain.com may not exist, adjust if needed)
        yield ['user@sub.domain.com', true];

        // IP literal (syntax valid, but DNS check may fail)
        yield ['user@[192.168.1.1]', false];  // Domain is [192.168.1.1], DNS check fails

        // International (IDN), but assuming ASCII for simplicity
        yield ['user@xn--caf-dma.com', true];  // punycode for café.com, but check actual

        yield ['<Special Expression 3.0> example', false];
    }

    public static function emailsDataProvider(): iterable
    {
        yield [
            'Это просто текст без каких-либо адресов.',
            0
        ];

        yield [
            'Контакт: john.doe@gmail.com для связи.',
            1
        ];

        yield [
            'Список: alice@mail.com, bob@site.net, carol@web.io.',
            2
        ];

        yield [
            '<a href="mailto:contact@gmail.com">contact us</a>',
            1
        ];

        yield [
            'Фейковый домен: ghost@nonexistentdomain.abc и реальный: valid@outlook.com', // outlook.com — MX есть
            1
        ];
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->emailService = ServiceContainer::getInstance()->make(EmailService::class);
    }

    #[Test]
    #[DataProvider('emailDataProvider')]
    public function testParseSingleEmail(string $email, bool $expected): void
    {
        $emails = $this->emailService->parseEmail($email);
        $this->assertEquals($expected, (bool)$emails);
    }

    #[Test]
    #[DataProvider('emailsDataProvider')]
    public function testParseMultipleEmails(string $email, int $count): void
    {
        $emails = $this->emailService->parseEmails($email);
        $this->assertCount($count, $emails);
    }
}
