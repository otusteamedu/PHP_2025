<?php

namespace tests\Unit\Validate;


use App\Validate\MaxLength;
use Codeception\Test\Unit;

class MaxLengthTest extends Unit
{
    public function testValidateReturnsNullWhenLengthIsValid(): void
    {
        $validator = new MaxLength();
        $result = $validator->validate('Hello', 10);

        $this->assertNull($result);
    }

//    public function testValidateReturnsJsonErrorWhenLengthIsExceeded(): void
//    {
//        $validator = new MaxLength();
//        $result = $validator->validate('This string is too long', 10);
//
//        $this->assertNotNull($result, 'Expected error message when string length exceeds limit');
//        $decoded = json_decode($result, true);
//
//        $this->assertIsArray($decoded);
//        $this->assertArrayHasKey('error', $decoded);
//        $this->assertStringContainsString('max', $decoded['error']);
//    }

}