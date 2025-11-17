<?php
declare(strict_types=1);

namespace Tests\Infrastructure\Http;

use App\Infrastructure\Http\Request;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SERVER = [];
    }

    public function testFromGlobalsParsesMethodPathAndHeaders(): void
    {
        $this->setWebServer();

        $request = Request::fromGlobals();

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('/validate/emails', $request->getPath());

        $this->assertTrue($request->hasHeader('x-custom'));
        $this->assertSame(['abc'], $request->getHeader('X-CUSTOM'));
        $this->assertSame(['application/json'], $request->getHeader('content-type'));
        $this->assertSame(['3'], $request->getHeader('Content-Length'));

        $this->assertSame('', (string)$request->getBody());
    }

    private function setWebServer(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'post';
        $_SERVER['REQUEST_URI'] = '/validate/emails?foo=bar';
        $_SERVER['HTTP_X_CUSTOM'] = 'abc';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $_SERVER['CONTENT_LENGTH'] = '3';
    }
}
