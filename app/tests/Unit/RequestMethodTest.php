<?php

namespace App\Tests\Unit;

use App\Validate\Message\RequestMethodMessage;
use App\Validate\RequestMethod;
use Codeception\Test\Unit;

class RequestMethodTest extends Unit
{
    public function testValid(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $method = 'POST';

        $handler = new RequestMethod();
        $result = $handler->validate($method);
        $this->assertNull($result);
    }

    public function testValueInvalid(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $method = 'POST';

        $handler = new RequestMethod();
        $result = $handler->validate($method);
        $message = new RequestMethodMessage($method);
        $this->assertEquals($result, json_encode($message->getMessage()));
    }

}