<?php

namespace App\Tests\Unit;

use App\Validate\MaxLength;
use App\Validate\MinLength;
use App\Validate\NotBlank;
use App\Validate\StringType;
use App\Validate\ValidationEmail;
use App\ValidateInputField\ValidateEmail;
use Codeception\Test\Unit;

class ValidateEmailTest extends Unit
{
    public function testValid(): void
    {
        $value = 'email@21vek.by';

        $handler = new ValidateEmail();
        $result = $handler->validate($value);
        $this->assertNull($result);
    }

    public function testEmailNull(): void
    {
        $value = null;

        $notBlank = new NotBlank();
        $notBlankValidate = $notBlank->validate($value);

        $handler = new ValidateEmail();
        $result = $handler->validate($value);
        $this->assertEquals($result, $notBlankValidate);
    }

    public function testEmailNotString(): void
    {
        $value = 2;

        $stringType = new StringType();
        $stringTypeValidate = $stringType->validate($value);

        $handler = new ValidateEmail();
        $result = $handler->validate($value);
        $this->assertEquals($result, $stringTypeValidate);
    }

    public function testEmailMaxLength(): void
    {
        $value = str_repeat('a', 246) . '@example.com';

        $maxLength = new MaxLength();
        $maxLengthValidate = $maxLength->validate($value, 245);

        $handler = new ValidateEmail();
        $result = $handler->validate($value);
        $this->assertEquals($result, $maxLengthValidate);
    }

    public function testEmailMinLength(): void
    {
        $value = 't@e.m';

        $minLength = new MinLength();
        $minLengthValidate = $minLength->validate($value, 5);

        $handler = new ValidateEmail();
        $result = $handler->validate($value);
        $this->assertEquals($result, $minLengthValidate);
    }

    public function testInvalidEmail(): void
    {
        $value = 'testValue';

        $validateEmail = new ValidationEmail();
        $validateEmailResult = $validateEmail->validate($value);

        $handler = new ValidateEmail();
        $result = $handler->validate($value);
        $this->assertEquals($result, $validateEmailResult);
    }
}
