<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Http;

use App\Infrastructure\Http\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testGetMethodReturnsPostWhenSet(): void
    {
        $request = new Request(['REQUEST_METHOD' => 'POST']);

        $this->assertEquals('POST', $request->getMethod());
    }

    public function testGetMethodReturnsGetWhenSet(): void
    {
        $request = new Request(['REQUEST_METHOD' => 'GET']);

        $this->assertEquals('GET', $request->getMethod());
    }

    public function testGetMethodReturnsGetByDefault(): void
    {
        $request = new Request([]);

        $this->assertEquals('GET', $request->getMethod());
    }

    public function testGetMethodConvertsToUppercase(): void
    {
        $request = new Request(['REQUEST_METHOD' => 'post']);

        $this->assertEquals('POST', $request->getMethod());
    }

    public function testGetMethodSupportsPut(): void
    {
        $request = new Request(['REQUEST_METHOD' => 'PUT']);

        $this->assertEquals('PUT', $request->getMethod());
    }

    public function testGetMethodSupportsDelete(): void
    {
        $request = new Request(['REQUEST_METHOD' => 'DELETE']);

        $this->assertEquals('DELETE', $request->getMethod());
    }

    public function testGetMethodSupportsPatch(): void
    {
        $request = new Request(['REQUEST_METHOD' => 'PATCH']);

        $this->assertEquals('PATCH', $request->getMethod());
    }

    public function testGetPathReturnsPath(): void
    {
        $request = new Request(['REQUEST_URI' => '/users']);

        $this->assertEquals('/users', $request->getPath());
    }

    public function testGetPathReturnsRootByDefault(): void
    {
        $request = new Request([]);

        $this->assertEquals('/', $request->getPath());
    }

    public function testGetPathStripsQueryString(): void
    {
        $request = new Request(['REQUEST_URI' => '/search?q=test&page=1']);

        $this->assertEquals('/search', $request->getPath());
    }

    public function testGetPathHandlesComplexPath(): void
    {
        $request = new Request(['REQUEST_URI' => '/api/v1/users/123/posts']);

        $this->assertEquals('/api/v1/users/123/posts', $request->getPath());
    }

    public function testGetPathHandlesEmptyUri(): void
    {
        $request = new Request(['REQUEST_URI' => '']);

        $this->assertEquals('/', $request->getPath());
    }

    public function testIsPostReturnsTrueForPostMethod(): void
    {
        $request = new Request(['REQUEST_METHOD' => 'POST']);

        $this->assertTrue($request->isPost());
    }

    public function testIsPostReturnsFalseForGetMethod(): void
    {
        $request = new Request(['REQUEST_METHOD' => 'GET']);

        $this->assertFalse($request->isPost());
    }

    public function testIsPostReturnsFalseByDefault(): void
    {
        $request = new Request([]);

        $this->assertFalse($request->isPost());
    }

    public function testGetBodyReturnsEmptyArrayByDefault(): void
    {
        $request = new Request([]);

        $this->assertIsArray($request->getBody());
        $this->assertEmpty($request->getBody());
    }
}
