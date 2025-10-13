<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Validate\MaxLength;
use App\Validate\Message\MaxLengthMessage;
use Codeception\Test\Unit;

class MaxLengthTest extends Unit
{
    public function testValidLength(): void
    {
        $value = 'hello world';
        $maxLength = 15;

        $handler = new MaxLength();
        $result = $handler->validate($value, $maxLength);
        $this->assertNull($result);
    }

    public function testTooLong(): void
    {
        $value = 'hello world';
        $maxLength = 5;

        $handler = new MaxLength();
        $result = $handler->validate($value, $maxLength);
        $message = new MaxLengthMessage($maxLength);
        $this->assertEquals($result, json_encode($message->getMessage()));
    }
}
