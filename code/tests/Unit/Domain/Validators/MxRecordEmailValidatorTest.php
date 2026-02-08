<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Validators;

use App\Domain\Validators\MxRecordEmailValidator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class MxRecordEmailValidatorTest extends TestCase
{
    private MxRecordEmailValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new MxRecordEmailValidator();
    }

    public function testValidateWithValidMxRecord(): void
    {
        $result = $this->validator->validate('user@example.com', 'email');

        $this->assertTrue($result);
        $this->assertSame('', $this->validator->getError());
    }

    public function testValidateWithInvalidDomain(): void
    {
        // Несуществующий домен
        $result = $this->validator->validate('admin@m.ru', 'email');

        $this->assertFalse($result);
        $this->assertSame('Для домена в поле email отсутствует MX запись', $this->validator->getError());
    }

    public function testValidateWithNoDomain(): void
    {
        $result = $this->validator->validate('test@', 'email');

        $this->assertFalse($result);
        $this->assertSame('Поле email должно содержать валидный домен', $this->validator->getError());
    }

    public function testValidateUsesCustomFieldName(): void
    {
        $result = $this->validator->validate('test@', 'user_email');

        $this->assertFalse($result);
        $this->assertSame('Поле user_email должно содержать валидный домен', $this->validator->getError());
    }
}
