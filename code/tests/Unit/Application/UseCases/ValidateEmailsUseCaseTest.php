<?php

declare(strict_types=1);

namespace Tests\Unit\Application\UseCases;

use App\Application\UseCases\ValidateEmailsUseCase;
use App\Domain\DTO\EmailValidationRequest;
use App\Domain\DTO\EmailValidationResult;
use App\Domain\Validators\EmailValidator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ValidateEmailsUseCaseTest extends TestCase
{
    private EmailValidator&MockObject $emailValidator;
    private ValidateEmailsUseCase $useCase;

    protected function setUp(): void
    {
        $this->emailValidator = $this->createMock(EmailValidator::class);
        $this->useCase = new ValidateEmailsUseCase($this->emailValidator);
    }

    public function testForEmptyRequest(): void
    {
        $request = new EmailValidationRequest([]);

        $results = $this->useCase->execute($request);

        $this->assertIsArray($results);
        $this->assertEmpty($results);
    }

    public function testForSingleValidEmail(): void
    {
        $request = new EmailValidationRequest(['test@example.com']);

        $this->emailValidator
            ->expects($this->once())
            ->method('validate')
            ->with('test@example.com')
            ->willReturn(true);

        $this->emailValidator
            ->method('getError')
            ->willReturn('');

        $results = $this->useCase->execute($request);

        $this->assertCount(1, $results);
        $this->assertInstanceOf(EmailValidationResult::class, $results[0]);
        $this->assertEquals('test@example.com', $results[0]->email);
        $this->assertTrue($results[0]->isValid);
        $this->assertEquals('', $results[0]->error);
    }

    public function testForSingleInvalidEmail(): void
    {
        $request = new EmailValidationRequest(['invalid-email']);

        $this->emailValidator
            ->expects($this->once())
            ->method('validate')
            ->with('invalid-email')
            ->willReturn(false);

        $this->emailValidator
            ->method('getError')
            ->willReturn('Invalid email format');

        $results = $this->useCase->execute($request);

        $this->assertCount(1, $results);
        $this->assertInstanceOf(EmailValidationResult::class, $results[0]);
        $this->assertEquals('invalid-email', $results[0]->email);
        $this->assertFalse($results[0]->isValid);
        $this->assertEquals('Invalid email format', $results[0]->error);
    }

    public function testForMultipleEmails(): void
    {
        $emails = ['valid@test.com', 'invalid', 'another@valid.org'];
        $request = new EmailValidationRequest($emails);

        $this->emailValidator
            ->expects($this->exactly(3))
            ->method('validate')
            ->willReturnCallback(function (string $email) {
                return $email !== 'invalid';
            });

        $this->emailValidator
            ->method('getError')
            ->willReturnCallback(function () {
                static $callCount = 0;
                $callCount++;
                return $callCount === 2 ? 'Invalid format' : '';
            });

        $results = $this->useCase->execute($request);

        $this->assertCount(3, $results);

        $this->assertEquals('valid@test.com', $results[0]->email);
        $this->assertTrue($results[0]->isValid);

        $this->assertEquals('invalid', $results[1]->email);
        $this->assertFalse($results[1]->isValid);
        $this->assertEquals('Invalid format', $results[1]->error);

        $this->assertEquals('another@valid.org', $results[2]->email);
        $this->assertTrue($results[2]->isValid);
    }
}
