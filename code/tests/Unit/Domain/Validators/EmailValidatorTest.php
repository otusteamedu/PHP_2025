<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Validators;

use App\Domain\Interfaces\ValidatorInterface;
use App\Domain\Validators\EmailValidator;
use PHPUnit\Framework\TestCase;

class EmailValidatorTest extends TestCase
{
    public function testValidateWithoutValidators(): void
    {
        $validator = new EmailValidator([]);

        $result = $validator->validate('any@email.com');

        $this->assertTrue($result);
        $this->assertSame('', $validator->getError());
    }

    public function testValidateWithPassingValidators(): void
    {
        $mockValidator1 = $this->createMock(ValidatorInterface::class);
        $mockValidator1->method('validate')->willReturn(true);

        $mockValidator2 = $this->createMock(ValidatorInterface::class);
        $mockValidator2->method('validate')->willReturn(true);

        $validator = new EmailValidator([$mockValidator1, $mockValidator2]);

        $result = $validator->validate('test@example.com');

        $this->assertTrue($result);
        $this->assertSame('', $validator->getError());
    }

    public function testValidateStopsOnFirstFailure(): void
    {
        $mockValidator1 = $this->createMock(ValidatorInterface::class);
        $mockValidator1->method('validate')->willReturn(false);
        $mockValidator1->method('getError')->willReturn('First error');

        $mockValidator2 = $this->createMock(ValidatorInterface::class);
        //$mockValidator2->expects($this->never())->method('validate');

        $validator = new EmailValidator([$mockValidator1, $mockValidator2]);

        $result = $validator->validate('test@example.com');

        $this->assertFalse($result);
        $this->assertSame('First error', $validator->getError());
    }

    public function testValidateReturnsSecondValidatorError(): void
    {
        $mockValidator1 = $this->createMock(ValidatorInterface::class);
        $mockValidator1->method('validate')->willReturn(true);

        $mockValidator2 = $this->createMock(ValidatorInterface::class);
        $mockValidator2->method('validate')->willReturn(false);
        $mockValidator2->method('getError')->willReturn('Second validator error');

        $validator = new EmailValidator([$mockValidator1, $mockValidator2]);

        $result = $validator->validate('test@example.com');

        $this->assertFalse($result);
        $this->assertSame('Second validator error', $validator->getError());
    }
}
