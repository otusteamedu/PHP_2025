<?php

namespace App\Tests\Unit;

use App\Validate\Message\ValidationEmailMessage;
use App\Validate\ValidationEmail;
use Codeception\Test\Unit;

class ValidationEmailTest extends Unit
{
    public function testValid(): void
    {
        $value = 'email@21vek.by';

        $handler = new ValidationEmail();
        $result = $handler->validate($value);
        $this->assertNull($result);
    }

    public function testValueInvalid(): void
    {
        $value = 'email';

        $handler = new ValidationEmail();
        $result = $handler->validate($value);
        $message = new ValidationEmailMessage();
        $this->assertEquals($result, json_encode($message->getMessage()));
    }

}