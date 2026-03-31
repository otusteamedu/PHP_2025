<?php

namespace App\Tests\Unit;

use App\Validate\Message\ValidationEmailMessage;
use Codeception\Test\Unit;

class ValidationEmailMessageTest extends Unit
{
    public function testValid(): void
    {
        $message = 'This email is not valid.';

        $handler = new ValidationEmailMessage();
        $result = $handler->getMessage();
        $this->assertEquals($result, $message);
    }
}
