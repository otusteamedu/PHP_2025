<?php

declare(strict_types=1);

namespace Tests\Http;

use App\Http\Request;
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
        $_SERVER['REQUEST_METHOD'] = 'post';
        $_SERVER['REQUEST_URI'] = '/validate-emails?foo=bar';
        $_SERVER['HTTP_X_CUSTOM'] = 'abc';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $_SERVER['CONTENT_LENGTH'] = '3';

        $req = Request::fromGlobals();

        $this->assertSame('POST', $req->getMethod());
        $this->assertSame('/validate-emails', $req->getPath());

        $this->assertTrue($req->hasHeader('x-custom'));
        $this->assertSame(['abc'], $req->getHeader('X-CUSTOM'));
        $this->assertSame(['application/json'], $req->getHeader('content-type'));
        $this->assertSame(['3'], $req->getHeader('Content-Length'));

        $this->assertSame('', (string)$req->getBody());
    }
}
