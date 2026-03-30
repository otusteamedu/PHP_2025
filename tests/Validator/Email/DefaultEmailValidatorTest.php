<?php

declare(strict_types=1);

namespace Tests\Validator\Email;

use App\Validator\DefaultEmailValidator;
use PHPUnit\Framework\TestCase;

class DefaultEmailValidatorTest extends TestCase
{
    private DefaultEmailValidator $validator;

    public function setUp(): void
    {
        error_reporting(E_ALL);

        $this->validator = new DefaultEmailValidator();
    }

    public function testValidEmail(): void
    {
        $validEmailList = [
            'test123@yandex.ru',
            'test1234@yandex.ru',
            'test2981eiuj@gmail.com',
        ];

        foreach ($validEmailList as $email) {
            $isValidEmail = $this->validator->isValidEmail($email);

            $this->assertTrue($isValidEmail);
        }
    }

    public function testInvalidEmail(): void
    {
        $validEmailList = [
            'test@test',
            'http://example.com',
            '',
        ];

        foreach ($validEmailList as $email) {
            $isValidEmail = $this->validator->isValidEmail($email);

            $this->assertFalse($isValidEmail);
        }
    }
}
