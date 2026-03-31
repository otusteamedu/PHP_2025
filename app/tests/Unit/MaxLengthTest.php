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

    public function testHiddenCharactersOnly(): void
    {
        $value = " \t\n\u{200B}\u{200C}";
        $maxLength = 5;

        $handler = new MaxLength();
        $result = $handler->validate($value, $maxLength);

        $message = new MaxLengthMessage($maxLength);
        $this->assertEquals($result, json_encode($message->getMessage()));
    }

    public function testMultilingualStringWithinLimit(): void
    {
        $value = 'Привет мир';
        $maxLength = 15;

        $handler = new MaxLength();
        $result = $handler->validate($value, $maxLength);

        $this->assertNull($result);
    }

    public function testMultilingualStringTooLong(): void
    {
        $value = '你好世界你好世界你好';
        $maxLength = 5;

        $handler = new MaxLength();
        $result = $handler->validate($value, $maxLength);

        $message = new MaxLengthMessage($maxLength);
        $this->assertEquals($result, json_encode($message->getMessage()));
    }

    public function testEmojiString(): void
    {
        $value = '👍👍👍';
        $maxLength = 2;

        $handler = new MaxLength();
        $result = $handler->validate($value, $maxLength);

        $message = new MaxLengthMessage($maxLength);
        $this->assertEquals($result, json_encode($message->getMessage()));
    }
}
