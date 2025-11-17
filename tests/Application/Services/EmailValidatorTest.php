<?php
declare(strict_types=1);

namespace Tests\Application\Services;

use App\Application\Services\EmailValidator;
use PHPUnit\Framework\TestCase;

final class EmailValidatorTest extends TestCase
{

    private const EMAIL_USER_NAME = 'testusername@';
    private const EMAIL_DOMAIN_NAME = 'testdomainname';
    private const EMAIL_IS_FAKE = 'testusername@testdomainname.testcountrycode';
    private const EMAIL_IS_REAL = 'testusername@testdomainname.ru';


    public function testInvalidFormatReturnsError(): void
    {
        $validator = new EmailValidator();
        $arResult = $validator->validate(['invalid']);

        $this->assertArrayHasKey('invalid', $arResult);
        $result = $arResult['invalid'];
        $this->assertFalse($result['is_valid']);
        $this->assertFalse($result['is_valid_format']);
        $this->assertNull($result['is_valid_dns']);
        $this->assertContains(EmailValidator::MESSAGE_VALIDATE_FORMAT, $result['errors']);
    }

    public function testEmailsListValidation(): void
    {
        $validator = new EmailValidator();
        $arEmails = [self::EMAIL_USER_NAME, self::EMAIL_DOMAIN_NAME, self::EMAIL_IS_FAKE, self::EMAIL_IS_REAL];

        echo print_r($arEmails, true);

        $arResult = $validator->validate($arEmails);

        $this->assertUserNameIsInvalidFormat($arResult);
        $this->assertDomainNameIsInvalidFormat($arResult);
        $this->assertLikelyIsNoValidMXFormat($arResult);
        $this->assertHasValidMXFormat($arResult);
    }

    private function assertUserNameIsInvalidFormat(array $arResult): void
    {
        $this->assertArrayHasKey(self::EMAIL_USER_NAME, $arResult);
        $result = $arResult[self::EMAIL_USER_NAME];
        $this->assertFalse($result['is_valid']);
        $this->assertFalse($result['is_valid_format']);
        $this->assertNull($result['is_valid_dns']);
        $this->assertContains(EmailValidator::MESSAGE_VALIDATE_FORMAT, $result['errors']);
    }

    private function assertDomainNameIsInvalidFormat(array $arResult): void
    {
        $this->assertArrayHasKey(self::EMAIL_DOMAIN_NAME, $arResult);
        $result = $arResult[self::EMAIL_DOMAIN_NAME];
        $this->assertFalse($result['is_valid']);
        $this->assertFalse($result['is_valid_format']);
        $this->assertNull($result['is_valid_dns']);
        $this->assertContains(EmailValidator::MESSAGE_VALIDATE_FORMAT, $result['errors']);
    }

    private function assertLikelyIsNoValidMXFormat(array $arResult): void
    {
        $this->assertArrayHasKey(self::EMAIL_IS_FAKE, $arResult);
        $result = $arResult[self::EMAIL_IS_FAKE];
        $this->assertFalse($result['is_valid']);
        $this->assertTrue($result['is_valid_format']);
        $this->assertFalse($result['is_valid_dns']);
        $this->assertContains(EmailValidator::MESSAGE_NO_MX_RECORDS, $result['errors']);
    }

    private function assertHasValidMXFormat(array $arResult): void
    {
        $this->assertArrayHasKey(self::EMAIL_IS_REAL, $arResult);
        $result = $arResult[self::EMAIL_IS_REAL];
        $this->assertTrue($result['is_valid']);
        $this->assertTrue($result['is_valid_format']);
        $this->assertTrue($result['is_valid_dns']);
        $this->assertEmpty($result['errors']);
    }
}
