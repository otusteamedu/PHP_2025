<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Validators;

use App\Domain\Validators\FormatEmailValidator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class FormatEmailValidatorTest extends TestCase
{
    private FormatEmailValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new FormatEmailValidator();
    }

    #[DataProvider('validEmailsProvider')]
    public function testValidateWithValidEmails(string $email): void
    {
        $result = $this->validator->validate($email, 'email');

        $this->assertTrue($result);
        $this->assertSame('', $this->validator->getError());
    }

    public static function validEmailsProvider(): array
    {
        return [
            'simple email' => ['test@example.com'],
            'with subdomain' => ['user@mail.example.com'],
            'with plus sign' => ['user+tag@example.com'],
            'with dots in local' => ['first.last@example.com'],
            'with numbers' => ['user123@example123.com'],
            'with underscore' => ['user_name@example.com'],
            'with hyphen in domain' => ['user@my-domain.com'],
            'uppercase' => ['USER@EXAMPLE.COM'],
            'mixed case' => ['User@Example.Com'],
            'long tld' => ['user@example.museum'],
            'percent in local' => ['user%tag@example.com'],
        ];
    }

    #[DataProvider('invalidEmailsProvider')]
    public function testValidateWithInvalidEmails(string $email): void
    {
        $result = $this->validator->validate($email, 'email');

        $this->assertFalse($result);
        $this->assertSame('Поле email должно быть валидным email адресом', $this->validator->getError());
    }

    public static function invalidEmailsProvider(): array
    {
        return [
            'no at sign' => ['testexample.com'],
            'no domain' => ['test@'],
            'no local part' => ['@example.com'],
            'double at' => ['test@@example.com'],
            'no tld' => ['test@example'],
            'space in email' => ['test @example.com'],
            'single char tld' => ['test@example.c'],
            'only at sign' => ['@'],
            'empty string' => [''],
            'special chars' => ['test!#$@example.com'],
        ];
    }

    public function testValidateWithNullValue(): void
    {
        $result = $this->validator->validate(null, 'email');

        $this->assertFalse($result);
        $this->assertSame('Поле email обязательно для заполнения', $this->validator->getError());
    }

    #[DataProvider('nonStringValuesProvider')]
    public function testValidateWithNonStringValues(mixed $value): void
    {
        $result = $this->validator->validate($value, 'email');

        $this->assertFalse($result);
        $this->assertSame('Поле email должно быть строкой', $this->validator->getError());
    }

    public static function nonStringValuesProvider(): array
    {
        return [
            'integer' => [123],
            'float' => [12.34],
            'boolean true' => [true],
            'boolean false' => [false],
            'array' => [['test@example.com']],
            'object' => [new \stdClass()],
        ];
    }

    public function testValidateUsesCustomFieldName(): void
    {
        $result = $this->validator->validate(null, 'user_email');

        $this->assertFalse($result);
        $this->assertSame('Поле user_email обязательно для заполнения', $this->validator->getError());
    }
}
