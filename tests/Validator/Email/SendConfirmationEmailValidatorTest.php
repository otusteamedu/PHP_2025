<?php

declare(strict_types=1);

namespace Tests\Validator\Email;

use App\RequestService\SomeProviderNameRequestService;
use App\Validator\SendConfirmationEmailValidator;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\Exception as MockException;
use PHPUnit\Framework\TestCase;

class SendConfirmationEmailValidatorTest extends TestCase
{
    public function setUp(): void
    {
        error_reporting(E_ALL);
    }

    /**
     * @throws Exception
     */
    public function testValidEmail(): void
    {
        $email = 'test123@yandex.ru';
        $validator = $this->mockValidator();

        $isValidEmail = $validator->isValidEmail($email);

        $this->assertTrue($isValidEmail);
    }

    /**
     * @throws Exception
     * @throws MockException
     */
    public function testInvalidEmail(): void
    {
        $email = 'test123@yandex.ru';
        $validator = $this->mockValidator(
            isEmailConfirmationSentReturnValue: false,
        );

        $isValidEmail = $validator->isValidEmail($email);

        $this->assertFalse($isValidEmail);
    }

    /**
     * @throws MockException
     */
    protected function mockValidator(
        bool $isEmailConfirmationSentReturnValue = true,
    ): SendConfirmationEmailValidator {
        $requestServiceMock = $this->createMock(SomeProviderNameRequestService::class);
        $requestServiceMock->method('isEmailConfirmationSent')->willReturn($isEmailConfirmationSentReturnValue);

        return new SendConfirmationEmailValidator($requestServiceMock);
    }
}
