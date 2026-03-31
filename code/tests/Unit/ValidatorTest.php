<?php

namespace Tests\Unit;

use App\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new Validator();
    }

    public function testValidateEmailWithValidEmail()
    {
        $result = $this->validator->validateEmail('test@example.com');
        $this->assertTrue($result);
    }

    public function testValidateEmailWithInvalidFormat()
    {
        $result = $this->validator->validateEmail('invalid-email');
        $this->assertFalse($result);
    }

    public function testValidateEmailWithEmptyString()
    {
        $result = $this->validator->validateEmail('');
        $this->assertFalse($result);
    }

    public function testValidateEmailWithNull()
    {
        $result = $this->validator->validateEmail(null);
        $this->assertFalse($result);
    }

    public function testValidateEmailWithInvalidDomain()
    {
        $result = $this->validator->validateEmail('user@nonexistent-domain-' . time() . '.xyz');
        $this->assertFalse($result);
    }

    public function testValidateEmailWithValidButComplexEmail()
    {
        $result = $this->validator->validateEmail('user.name+tag@sub.domain.co.uk');
        $this->assertTrue($result);
    }

    public function testValidateEmailWithInvalidCharacters()
    {
        $result = $this->validator->validateEmail('invalid@email@domain.com');
        $this->assertFalse($result);
    }

    public function testValidateEmailWithoutAtSymbol()
    {
        $result = $this->validator->validateEmail('invalid.email.com');
        $this->assertFalse($result);
    }
}