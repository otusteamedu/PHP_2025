<?php

namespace EmailsVerifier\Tests\Unit;

use EmailsVerifier\Domain\Email;
use EmailsVerifier\Domain\Validators\FormatValidator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use EmailsVerifier\Tests\ValidationErrorHelper;

class FormatValidatorTest extends TestCase
{

    private const LIMIT_CHARS_LOCAL_PART = 64;
    private const LIMIT_CHARS_DOMAIN = 255;

    private FormatValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new FormatValidator();
    }

    #[DataProvider('validEmailProvider')]
    public function testValidEmail(string $email): void
    {
        $this->assertEmpty($this->validator->validate(new Email($email)));
    }

    public static function validEmailProvider(): array
    {
        return [
            'SIMPLE' => ['test@example.ru'],
            'WITH_DOT_IN_NAME' => ['user.name@domain.org'],
            'WITH_PLUS' => ['user+tag@example.ru'],
            'WITH_SUBDOMAIN' => ['first.last@sub.domain.ru'],
            'LONG_LOCAL_PART' => [str_repeat('a', self::LIMIT_CHARS_LOCAL_PART) . '@example.ru'],
            'LONG_DOMAIN' => ['user@' . str_repeat('a', self::LIMIT_CHARS_DOMAIN - 3) . '.ru'],
        ];
    }

    #[DataProvider('invalidEmailProvider')]
    public function testInvalidEmail(string $email, string $expectedError): void
    {
        $errors = $this->validator->validate(new Email($email));
        $this->assertContains($expectedError, ValidationErrorHelper::getErrorMessages($errors));
    }

    public static function invalidEmailProvider(): array
    {
        return [
            'NO_AT_SYMBOL' => ['invalid', 'Адрес не соответствует шаблону'],
            'NO_DOMAIN' => ['invalid@', 'Адрес не соответствует шаблону'],
            'NO_LOCAL_PART' => ['@example.ru', 'Адрес не соответствует шаблону'],
            'DOMAIN_STARTS_WITH_DOT' => ['test@.ru', 'Домен начинается с точки'],
            'LOCAL_PART_STARTS_WITH_DOT' => ['.test@example.ru', 'Локальная часть адреса начинается или заканчивается точкой'],
            'LOCAL_PART_ENDS_WITH_DOT' => ['test.@example.ru', 'Локальная часть адреса начинается или заканчивается точкой'],
            'DOUBLE_DOT' => ['test..user@example.ru', 'В адресе найдены две точки подряд'],
            'MULTIPLE_AT_SYMBOLS' => ['test@user@example.ru', 'Некорректное количество символов @'],
            'CYRILLIC_IN_LOCAL_PART' => ['тест@example.ru', 'Адрес не соответствует шаблону'],
            'CYRILLIC_IN_DOMAIN' => ['test@пример.ru', 'Адрес не соответствует шаблону'],
            'SPACE_IN_EMAIL' => ['test user@example.ru', 'Адрес не соответствует шаблону'],
            'PLUS_IN_DOMAIN' => ['test@exam+ple.ru', 'Адрес не соответствует шаблону'],
        ];
    }
}
