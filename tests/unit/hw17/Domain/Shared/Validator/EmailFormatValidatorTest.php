<?php

declare(strict_types=1);

namespace UnitTests\hw17\Domain\Shared\Validator;

use App\Domain\Shared\Validator\EmailFormatValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EmailFormatValidatorTest extends TestCase
{
    /**
     * Если email имеет валидный формат, то валидатор возвращает true.
     */
    #[DataProvider('validEmailsProvider')]
    public function testValidEmailsReturnsTrue(string $email): void
    {
        $validator = new EmailFormatValidator();

        $this->assertTrue($validator->isValid($email));
    }

    /**
     * Если email имеет невалидный формат, то валидатор возвращает false.
     */
    #[DataProvider('invalidEmailsProvider')]
    public function testInvalidEmailsReturnsFalse(string $email): void
    {
        $validator = new EmailFormatValidator();

        $this->assertFalse($validator->isValid($email));
    }

    public static function validEmailsProvider(): array
    {
        return [
            // Базовые форматы
            'valid-simple'                  => ['test@example.com'],
            'valid-mixed-case'              => ['TeSt@ExAmPlE.cOm'],

            // Plus-addressing
            'valid-plus-newsletter'         => ['subscriber+newsletter@mail.ru'],
            'valid-plus-tracking'           => ['client+track-123@yandex.ru'],

            // Домены: поддомены и новые TLD
            'valid-subdomain'               => ['user@mail.sub.example.com'],
            'valid-new-tld-xyz'             => ['user@example.xyz'],
            'valid-new-tld-tech'            => ['user@site.tech'],

            // Локальная часть в кавычках
            'valid-quoted-minimal'          => ['"user"@example.com'],
            'valid-quoted-with-dot'         => ['"user.name"@example.com'],
            'valid-quoted-with-at'          => ['"user@name"@example.com'],

            // Допустимые спецсимволы без кавычек
            'valid-local-with-dot'          => ['first.last@example.com'],
            'valid-local-with-hyphen'       => ['first-last@example.com'],
            'valid-local-with-underscore'   => ['first_last@example.com'],
            'valid-local-with-percent'      => ['first%last@example.com'],
        ];
    }

    public static function invalidEmailsProvider(): array
    {
        return [
            // Пустота и почти пустота
            'invalid-empty'                 => [''],
            'invalid-whitespace'            => [' '],

            // Отсутствие или лишние символы @
            'invalid-no-at'                 => ['userexample.com'],
            'invalid-double-at'             => ['user@@example.com'],

            // Отсутствие локальной части или домена
            'invalid-missing-local'         => ['@example.com'],
            'invalid-missing-domain'        => ['user@'],

            // Недопустимые символы без кавычек
            'invalid-space-in-local'        => ['user name@example.com'],
            'invalid-brackets-in-local'     => ['user(name)@example.com'],
            'invalid-comma-in-local'        => ['user,name@example.com'],

            // Проблемы в доменной части
            'invalid-no-tld'                => ['user@example'],
            'invalid-double-dot-domain'     => ['user@example..com'],
            'invalid-underscore-in-domain'  => ['user@exa_mple.com'],
            'invalid-whitespace-in-domain'  => ['user@ example.com'],
            'invalid-domain-starts-dot'     => ['user@.example.com'],

            // Граничные случаи
            'invalid-double-dot-local'      => ['user..name@example.com'],
            'invalid-dot-start-local'       => ['.user@example.com'],
            'invalid-dot-end-local'         => ['user.@example.com'],
        ];
    }
}
