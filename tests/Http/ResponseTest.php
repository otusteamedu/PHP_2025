<?php

declare(strict_types=1);

namespace Tests\Http;

use App\Http\Response;
use App\Http\Stream;
use PHPUnit\Framework\TestCase;

final class ResponseTest extends TestCase
{
    public function testDefaults(): void
    {
        $r = new Response();
        $this->assertSame(200, $r->getStatusCode());
        $this->assertSame([], $r->getHeaders());
        $this->assertInstanceOf(Stream::class, $r->getBody());
        $this->assertSame('', (string)$r->getBody());
    }

    public function testWithStatusIsImmutable(): void
    {
        $r1 = new Response();
        $r2 = $r1->withStatus(404);
        $this->assertNotSame($r1, $r2);
        $this->assertSame(200, $r1->getStatusCode());
        $this->assertSame(404, $r2->getStatusCode());
    }

    public function testHeadersManipulation(): void
    {
        $r = new Response();
        $r2 = $r->withHeader('Content-Type', 'application/json');
        $this->assertFalse($r->hasHeader('content-type'));
        $this->assertTrue($r2->hasHeader('content-type'));
        $this->assertSame(['application/json'], $r2->getHeader('CONTENT-TYPE'));

        $r3 = $r2->withAddedHeader('content-type', 'charset=utf-8');
        $this->assertSame(['application/json', 'charset=utf-8'], $r3->getHeader('Content-Type'));

        $r4 = $r3->withoutHeader('CONTENT-TYPE');
        $this->assertFalse($r4->hasHeader('Content-Type'));
    }

    public function testWithBodyIsImmutable(): void
    {
        $r1 = new Response();
        $r2 = $r1->withBody(new Stream('x'));
        $this->assertNotSame($r1, $r2);
        $this->assertSame('', (string)$r1->getBody());
        $this->assertSame('x', (string)$r2->getBody());
    }
}
