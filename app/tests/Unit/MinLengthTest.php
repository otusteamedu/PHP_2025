<?php

namespace App\Tests\Unit;

use App\Validate\Message\MinLengthMessage;
use App\Validate\MinLength;
use Codeception\Test\Unit;

class MinLengthTest extends Unit
{
    public function testValidLength(): void
    {
        $value = 'hello world';
        $maxLength = 5;

        $handler = new MinLength();
        $result = $handler->validate($value, $maxLength);
        $this->assertNull($result);
    }

    public function testTooLong(): void
    {
        $value = 'hello world';
        $maxLength = 15;

        $handler = new MinLength();
        $result = $handler->validate($value, $maxLength);
        $message = new MinLengthMessage($maxLength);
        $this->assertEquals($result, json_encode($message->getMessage()));
    }

}