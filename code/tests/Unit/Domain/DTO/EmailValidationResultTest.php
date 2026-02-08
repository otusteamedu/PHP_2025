<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\DTO;

use App\Domain\DTO\EmailValidationResult;
use PHPUnit\Framework\TestCase;

class EmailValidationResultTest extends TestCase
{
    public function testSetProperties(): void
    {
        $result = new EmailValidationResult(
            email: 'test@example.com',
            isValid: true,
            error: ''
        );

        $this->assertSame('test@example.com', $result->email);
        $this->assertTrue($result->isValid);
        $this->assertSame('', $result->error);
    }

    public function testSetPropertiesWithError(): void
    {
        $result = new EmailValidationResult(
            email: 'invalid-email',
            isValid: false,
            error: 'Invalid email format'
        );

        $this->assertSame('invalid-email', $result->email);
        $this->assertFalse($result->isValid);
        $this->assertSame('Invalid email format', $result->error);
    }

    public function testDefaultErrorIsEmptyString(): void
    {
        $result = new EmailValidationResult(
            email: 'test@example.com',
            isValid: true
        );

        $this->assertSame('', $result->error);
    }

    public function testToArrayWithValidEmail(): void
    {
        $result = new EmailValidationResult(
            email: 'test@example.com',
            isValid: true
        );

        $expected = [
            'email' => 'test@example.com',
            'is_valid' => true,
        ];

        $this->assertSame($expected, $result->toArray());
    }

    public function testToArrayWithInvalidEmailAndError(): void
    {
        $result = new EmailValidationResult(
            email: 'invalid',
            isValid: false,
            error: 'Invalid email format'
        );

        $expected = [
            'email' => 'invalid',
            'is_valid' => false,
            'error' => 'Invalid email format',
        ];

        $this->assertSame($expected, $result->toArray());
    }

    public function testToArrayDoesNotIncludeEmptyError(): void
    {
        $result = new EmailValidationResult(
            email: 'test@example.com',
            isValid: false,
            error: ''
        );

        $array = $result->toArray();

        $this->assertArrayNotHasKey('error', $array);
    }
}
