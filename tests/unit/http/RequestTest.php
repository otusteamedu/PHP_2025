<?php

use PHPUnit\Framework\TestCase;
use App\http\Request;

// тесты для Request::__construct()
class RequestTest extends TestCase
{
    //позитивный
    public function testValidRequest(): void
    {
        $req = new Request(['emails' => "user@example.com\nother@test.com"]);
        $this->assertSame("user@example.com\nother@test.com", $req->getInput());
    }

    //негативные
    public function testMissingEmailsField(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Request([]);
    }

    public function testNonStringEmailsField(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Request(['emails' => ['not', 'a', 'string']]);
    }

    public function testEmptyEmailsField(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Request(['emails' => "   "]);
    }
}
