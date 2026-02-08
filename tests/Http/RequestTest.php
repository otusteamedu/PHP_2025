<?php

namespace Tests\Http;

use App\Http\Request;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function setUp(): void
    {
        parent::setUp();
        $_SERVER = [];
        $_POST = [];
    }

    public function testIsPostTrue(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $request = new Request();

        $result = $request->isPost();

        $this->assertTrue($result);
    }

    public function testIsPostFalse(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $request = new Request();

        $result = $request->isPost();

        $this->assertFalse($result);
    }

    public function testGetEmailsInputReturnsTrimmed(): void
    {
        $_POST['emails'] = "  a@example.com  \n";
        $request = new Request();

        $result = $request->getEmailsInput();

        $this->assertSame("a@example.com", $result);
    }

    public function testGetEmailsInputReturnsEmptyString(): void
    {
        $request = new Request();

        $result = $request->getEmailsInput();

        $this->assertSame('', $result);
    }
}
