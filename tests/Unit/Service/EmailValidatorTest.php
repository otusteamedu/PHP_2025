<?php

declare(strict_types=1);

namespace Unit\Service;

use Dinargab\Homework5\Result\ValidationResult;
use Dinargab\Homework5\Service\EmailValidator;
use Dinargab\Homework5\Service\FormatterInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EmailValidatorTest extends TestCase
{
    private EmailValidator $validator;

    public function setUp(): void
    {
        $this->validator = new EmailValidator();
    }

    public static function validEmailProvider(): array
    {
        return [
            ['help@otus.ru'],
            ['niceandsimple@example.com'],
            ['very.common@example.com'],
            ['a.little.lengthy.but.fine@example.com'],
            ['disposable.style.email.with+symbol@example.com'],
            ['test@example.com'],
            ["!#$%&'*+-/=?^_`{}|~@example.org"],
        ];
    }


    #[DataProvider('validEmailProvider')]
    public function testVerifyValidEmail(string $email): void
    {
        $result = $this->validator->verifyEmail($email);

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertIsBool($result->isValid());
        $this->assertTrue($result->isValid());
        $this->assertNull($result->getError());
    }

    public static function invalidEmailProvider(): array
    {
        return [
            ['', 'Email is empty'],
            ['a' . str_repeat('b', 250) . '@example.com', "Email is longer than 254 characters"],
            ['no-mx@nomxdomain.org', 'No MX records found for email domain'],
            ["\"()<>[]:,;@\\\"!#$%&'*+-/=?^_`\{}| ~.a\"@example.com", 'Invalid email format'],
            ['test@localhost', 'Invalid email format'],
            ['" "@example.com', 'Invalid email format'],
            ['üñîçøðé@example.com', 'Invalid email format'],
        ];
    }

    #[DataProvider('invalidEmailProvider')]
    public function testVerifyInvalidEmail(string $email, string $message): void
    {
        $result = $this->validator->verifyEmail($email);
        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertIsBool($result->isValid());
        $this->assertFalse($result->isValid());
        $this->assertIsString($result->getError());
        $this->assertSame($message, $result->getError());
    }


    public static function invalidEmailsArrayDataProvider(): array
    {
        return [
            [
                [
                    ['email' => '', 'valid' => false, 'message' => 'Email is empty'],
                    ['email' => 'niceandsimple@example.com', 'valid' => true, 'message' => null],
                    ['email' => 'very.common@example.com', 'valid' => true, 'message' => null],
                    ['email' => 'a.little.lengthy.but.fine@example.com', 'valid' => true, 'message' => null],
                    ['email' => 'a' . str_repeat('b', 250) . '@example.com', 'valid' => false, 'message' => "Email is longer than 254 characters"],
                    ['email' => 'test@example.com', 'valid' => true, 'message' => null]
                ]
            ],
            [
                [
                    ['email' => 'no-mx@nomxdomain.org', 'valid' => false, 'message' => 'No MX records found for email domain'],
                    ['email' => 'disposable.style.email.with+symbol@example.com', 'valid' => true, 'message' => null],
                    ['email' => "\"()<>[]:,;@\\\"!#$%&'*+-/=?^_`\{}| ~.a\"@example.com", 'valid' => false, 'message' => 'Invalid email format'],
                    ['email' => 'test@localhost', 'valid' => false, 'message' => 'Invalid email format'],
                    ['email' => '" "@example.com', 'valid' => false, 'message' => 'Invalid email format'],
                    ['email' => 'üñîçøðé@example.com', 'valid' => false, 'message' => 'Invalid email format'],
                ]
            ]
        ];
    }

    #[DataProvider('invalidEmailsArrayDataProvider')]
    public function testVerifyEmailArray(array $emails): void
    {
        $result = $this->validator->verifyEmails(array_column($emails, 'email'), false);
        $this->assertIsArray($result);
        $this->assertCount(count($emails), $result);

        foreach ($result as $key => $emailValidationResult) {
            $this->assertInstanceOf(ValidationResult::class, $emailValidationResult);
            $this->assertIsBool($emailValidationResult->isValid());
            if ($emails[$key]['valid']) {
                $this->assertTrue($emailValidationResult->isValid());
                $this->assertNull($emailValidationResult->getError());
            } else {
                $this->assertFalse($emailValidationResult->isValid());
                $this->assertSame($emailValidationResult->getError(), $emails[$key]['message']);
            }
        }
    }


    public function testVerifyEmailsWithEmptyArray(): void
    {
        $result = $this->validator->verifyEmails([]);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testVerifyEmailsWithFormatResult(): void
    {
        $emails = [
            'test@gmail.com',
            'invalid-email'
        ];

        $result = $this->validator->verifyEmails($emails, true);

        $this->assertIsString($result);
        $this->assertStringContainsString('test@gmail.com is valid', $result);
        $this->assertStringContainsString('invalid-email is invalid', $result);
        $this->assertStringContainsString('<br>', $result);
    }


    public function testSetFormatter(): void
    {
        $mockFormatter = $this->createMock(FormatterInterface::class);

        $mockFormatter
            ->method('format')
            ->willReturn("formatted_emails_list");

        $this->validator->setFormatter($mockFormatter);

        $emails = ['test@gmail.com'];
        $result = $this->validator->verifyEmails($emails, true);

        $this->assertSame('formatted_emails_list', $result);
    }

}
