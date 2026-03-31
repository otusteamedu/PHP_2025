<?php

namespace App\Tests\Unit;

use App\Validate\Message\RequestMethodMessage;
use App\ValidateInputField\ValidateRequestMethodPost;
use Codeception\Test\Unit;

class ValidateRequestMethodPostTest extends Unit
{
    public function testValid(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $handler = new ValidateRequestMethodPost();
        $result = $handler->validate();
        $this->assertNull($result);
    }

    public function testValueInvalid(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $method = 'POST';

        $handler = new ValidateRequestMethodPost();
        $result = $handler->validate();
        $message = new RequestMethodMessage($method);
        $this->assertEquals($result, json_encode($message->getMessage()));
    }
}
