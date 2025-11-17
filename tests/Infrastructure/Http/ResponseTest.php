<?php
declare(strict_types=1);

namespace Tests\Infrastructure\Http;

use App\Infrastructure\Http\Response;
use App\Infrastructure\Http\Stream;
use PHPUnit\Framework\TestCase;

final class ResponseTest extends TestCase
{
    public function testDefaults(): void
    {
        $response = new Response();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([], $response->getHeaders());
        $this->assertInstanceOf(Stream::class, $response->getBody());
        $this->assertSame('', (string)$response->getBody());
    }

    public function testWithStatusIsImmutable(): void
    {
        $response_1 = new Response();
        $response_2 = $response_1->withStatus(404);
        $this->assertNotSame($response_1, $response_2);
        $this->assertSame(200, $response_1->getStatusCode());
        $this->assertSame(404, $response_2->getStatusCode());
    }

    public function testHeadersManipulation(): void
    {
        $response_1 = new Response();
        $response_2 = $response_1->withHeader('Content-Type', 'application/json');
        $this->assertFalse($response_1->hasHeader('content-type'));
        $this->assertTrue($response_2->hasHeader('content-type'));
        $this->assertSame(['application/json'], $response_2->getHeader('CONTENT-TYPE'));

        $response_3 = $response_2->withAddedHeader('content-type', 'charset=utf-8');
        $this->assertSame(['application/json', 'charset=utf-8'], $response_3->getHeader('Content-Type'));

        $response_4 = $response_3->withoutHeader('CONTENT-TYPE');
        $this->assertFalse($response_4->hasHeader('Content-Type'));
    }

    public function testWithBodyIsImmutable(): void
    {
        $response_1 = new Response();
        $response_2 = $response_1->withBody(new Stream('x'));
        $this->assertNotSame($response_1, $response_2);
        $this->assertSame('', (string)$response_1->getBody());
        $this->assertSame('x', (string)$response_2->getBody());
    }
}
