<?php

declare(strict_types=1);

namespace UnitTests\hw17\Domain\Shared\Validator;

use App\Domain\Shared\Validator\DnsMxRecordValidator;
use App\Domain\Shared\Validator\EmailFormatValidator;
use App\Domain\Shared\Validator\EmailValidator;
use PHPUnit\Framework\TestCase;

class EmailValidatorTest extends TestCase
{
    /**
     * Если формат email невалиден, то композитный валидатор сразу возвращает false,
     * и DNS‑валидатор не вызывается.
     */
    public function testInvalidFormatReturnsFalse(): void
    {
        $email = 'not-an-email';

        $formatMock = $this->createMock(EmailFormatValidator::class);
        $formatMock
            ->expects($this->once())
            ->method('isValid')
            ->with($email)
            ->willReturn(false);

        $validator = new EmailValidator($formatMock, null);

        $this->assertFalse($validator->isValid($email));
    }

    /**
     * Если формат email валиден и DNS‑валидатор отсутствует, то валидатор возвращает true.
     */
    public function testValidFormatWithNoDnsValidatorReturnsTrue(): void
    {
        $email = 'user@example.com';

        $formatMock = $this->createMock(EmailFormatValidator::class);
        $formatMock
            ->expects($this->once())
            ->method('isValid')
            ->with($email)
            ->willReturn(true);

        $validator = new EmailValidator($formatMock, null);

        $this->assertTrue($validator->isValid($email));
    }

    /**
     * Если формат email валиден и домен имеет MX‑запись, то валидатор возвращает true.
     */
    public function testValidFormatAndGoodDomainReturnsTrue(): void
    {
        $email = 'user@example.com';
        $domain = 'example.com';

        $formatMock = $this->createMock(EmailFormatValidator::class);
        $formatMock
            ->expects($this->once())
            ->method('isValid')
            ->with($email)
            ->willReturn(true);

        $dnsMock = $this->createMock(DnsMxRecordValidator::class);
        $dnsMock
            ->expects($this->once())
            ->method('isValid')
            ->with($domain)
            ->willReturn(true);

        $validator = new EmailValidator($formatMock, $dnsMock);

        $this->assertTrue($validator->isValid($email));
    }

    /**
     * Если формат email валиден, но домен не имеет MX‑записи, то валидатор возвращает false.
     */
    public function testValidFormatButBadDomainReturnsFalse(): void
    {
        $email = 'user@no-mx-domain.test';
        $domain = 'no-mx-domain.test';

        $formatMock = $this->createMock(EmailFormatValidator::class);
        $formatMock
            ->expects($this->once())
            ->method('isValid')
            ->with($email)
            ->willReturn(true);

        $dnsMock = $this->createMock(DnsMxRecordValidator::class);
        $dnsMock
            ->expects($this->once())
            ->method('isValid')
            ->with($domain)
            ->willReturn(false);

        $validator = new EmailValidator($formatMock, $dnsMock);

        $this->assertFalse($validator->isValid($email));
    }
}
