<?php

namespace App\Tests\Unit;

use App\Validate\Message\StringTypeMessage;
use App\Validate\StringType;
use Codeception\Test\Unit;

class StringTypeTest extends Unit
{
    public function testValid(): void
    {
        $value = 'hello';

        $handler = new StringType();
        $result = $handler->validate($value);
        $this->assertNull($result);
    }

    public function testValueInvalid(): void
    {
        $value = 3;

        $handler = new StringType();
        $result = $handler->validate($value);
        $message = new StringTypeMessage();
        $this->assertEquals($result, json_encode($message->getMessage()));
    }
}
