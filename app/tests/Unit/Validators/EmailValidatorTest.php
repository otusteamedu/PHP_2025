<?php declare(strict_types=1);

namespace Tests\Unit\Validators;

use App\Validators\EmailValidator;
use App\Validators\ValidationException;
use DomainException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class EmailValidatorTest
 * @package Tests\Unit\Validators
 */
class EmailValidatorTest extends TestCase
{
    public function testThrowDomainExceptionWhenIdnEnabledWithoutIntlExtension(): void
    {
        if (function_exists('idn_to_ascii')) {
            $this->markTestSkipped('intl extension is available');
        }

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('In order to use IDN validation intl extension must be installed and enabled.');

        new EmailValidator(false, true);
    }

    #[DataProvider('validFormatEmailWithIdnDisabledProvider')]
    public function testValidFormatEmailsWithIdnDisabled(string $email): void
    {
        $this->expectNotToPerformAssertions();

        $validator = new EmailValidator(false, false);
        $validator->validate($email);
    }

    #[DataProvider('invalidFormatEmailWithIdnDisabledProvider')]
    public function testInvalidFormatEmailsWithIdnDisabled(string $email): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid format');

        $validator = new EmailValidator(false, false);
        $validator->validate($email);
    }

    public function testValidFormatIdnEmailWithIdnEnabled(): void
    {
        if (!function_exists('idn_to_ascii')) {
            $this->markTestSkipped('intl extension is not available');
        }

        $validator = new EmailValidator(false, true);
        $this->expectNotToPerformAssertions();

        $validator->validate('тест@абракадабра.рус');
    }

    public function testValidDnsEmailWithDnsValidationEnabled(): void
    {
        $this->expectNotToPerformAssertions();

        $validator = new EmailValidator(true, false);
        $validator->validate('test@google.com');
    }

    public function testInvalidDnsEmailWithDnsValidationEnabled(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid DNS');

        $validator = new EmailValidator(true, false);
        $validator->validate('test@google.con');
    }

    public function testValidEmailWithDnsValidationAndIdnEnabled(): void
    {
        if (!function_exists('idn_to_ascii')) {
            $this->markTestSkipped('intl extension is not available');
        }

        $validator = new EmailValidator(true, true);
        $this->expectNotToPerformAssertions();

        $validator->validate('тест@почта.рус');
    }

    public static function validFormatEmailWithIdnDisabledProvider(): array
    {
        return [
            ['user@domain.com'],
            ['user.name@domain.com'],
            ['user123@domain.com'],
            ['user@example-domain.com'],
            ['user@domain.co.uk'],
            ['a@b.co'],
            ['xn--e1aybc@xn--80a1acny.xn--p1acf'],
        ];
    }

    public static function invalidFormatEmailWithIdnDisabledProvider(): array
    {
        return [
            [''],
            ['invalid'],
            ['@example.com'],
            ['user@'],
            ['user@.com'],
            ['user@com'],
            ['user.@example.com'],
            ['.user@example.com'],
            ['user..name@example.com'],
            ['user@example..com'],
            ['user@-example.com'],
            ['user@example-.com'],
            ['user name@example.com'],
            ['user@example domain.com'],
            ['user@@example.com'],
            ['user@example@com'],
            ['user@.example.com'],
            ['user@example.'],
            ['user@example.co.'],
            ['тест@абракадабра.рус'],
        ];
    }
}
