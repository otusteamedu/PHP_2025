<?php

namespace App\Tests\Unit;

use App\Validate\Message\NotBlankMessage;
use App\Validate\NotBlank;
use Codeception\Test\Unit;

class NotBlankTest extends Unit
{
    public function testValid(): void
    {
        $value = 'hello world';

        $handler = new NotBlank();
        $result = $handler->validate($value);
        $this->assertNull($result);
    }

    public function testValueNull(): void
    {
        $value = null;

        $handler = new NotBlank();
        $result = $handler->validate($value);
        $message = new NotBlankMessage();
        $this->assertEquals($result, json_encode($message->getMessage()));
    }

}