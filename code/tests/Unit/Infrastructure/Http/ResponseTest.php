<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Http;

use App\Infrastructure\Http\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testConstructorSetsContent(): void
    {
        $response = new Response('test content');

        $reflection = new \ReflectionClass($response);
        $property = $reflection->getProperty('content');
        $property->setAccessible(true);

        $this->assertEquals('test content', $property->getValue($response));
    }

    public function testConstructorSetsStatusCode(): void
    {
        $response = new Response('', 404);

        $reflection = new \ReflectionClass($response);
        $property = $reflection->getProperty('statusCode');
        $property->setAccessible(true);

        $this->assertEquals(404, $property->getValue($response));
    }

    public function testConstructorSetsHeaders(): void
    {
        $headers = ['Content-Type' => 'text/plain'];
        $response = new Response('', 200, $headers);

        $reflection = new \ReflectionClass($response);
        $property = $reflection->getProperty('headers');
        $property->setAccessible(true);

        $this->assertEquals($headers, $property->getValue($response));
    }

    public function testConstructorDefaultValues(): void
    {
        $response = new Response();

        $reflection = new \ReflectionClass($response);

        $contentProperty = $reflection->getProperty('content');
        $contentProperty->setAccessible(true);

        $statusCodeProperty = $reflection->getProperty('statusCode');
        $statusCodeProperty->setAccessible(true);

        $headersProperty = $reflection->getProperty('headers');
        $headersProperty->setAccessible(true);

        $this->assertEquals('', $contentProperty->getValue($response));
        $this->assertEquals(200, $statusCodeProperty->getValue($response));
        $this->assertEquals([], $headersProperty->getValue($response));
    }

    public function testSuccessReturnsResponseInstance(): void
    {
        $response = Response::success(['test' => 'data']);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testSuccessCreatesCorrectJsonContent(): void
    {
        $response = Response::success(['key' => 'value']);

        $reflection = new \ReflectionClass($response);
        $property = $reflection->getProperty('content');
        $property->setAccessible(true);

        $content = json_decode($property->getValue($response), true);

        $this->assertTrue($content['success']);
        $this->assertEquals(['key' => 'value'], $content['data']);
    }

    public function testSuccessSetsDefaultStatusCode200(): void
    {
        $response = Response::success([]);

        $reflection = new \ReflectionClass($response);
        $property = $reflection->getProperty('statusCode');
        $property->setAccessible(true);

        $this->assertEquals(200, $property->getValue($response));
    }

    public function testSuccessAllowsCustomStatusCode(): void
    {
        $response = Response::success([], 201);

        $reflection = new \ReflectionClass($response);
        $property = $reflection->getProperty('statusCode');
        $property->setAccessible(true);

        $this->assertEquals(201, $property->getValue($response));
    }

    public function testSuccessSetsJsonContentType(): void
    {
        $response = Response::success([]);

        $reflection = new \ReflectionClass($response);
        $property = $reflection->getProperty('headers');
        $property->setAccessible(true);

        $headers = $property->getValue($response);

        $this->assertEquals('application/json', $headers['Content-Type']);
    }

    public function testErrorReturnsResponseInstance(): void
    {
        $response = Response::error('Error message');

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testErrorCreatesCorrectJsonContent(): void
    {
        $response = Response::error('Something went wrong');

        $reflection = new \ReflectionClass($response);
        $property = $reflection->getProperty('content');
        $property->setAccessible(true);

        $content = json_decode($property->getValue($response), true);

        $this->assertFalse($content['success']);
        $this->assertEquals('Something went wrong', $content['error']);
    }

    public function testErrorSetsDefaultStatusCode400(): void
    {
        $response = Response::error('Error');

        $reflection = new \ReflectionClass($response);
        $property = $reflection->getProperty('statusCode');
        $property->setAccessible(true);

        $this->assertEquals(400, $property->getValue($response));
    }

    public function testErrorAllowsCustomStatusCode(): void
    {
        $response = Response::error('Not found', 404);

        $reflection = new \ReflectionClass($response);
        $property = $reflection->getProperty('statusCode');
        $property->setAccessible(true);

        $this->assertEquals(404, $property->getValue($response));
    }

    public function testErrorSetsJsonContentType(): void
    {
        $response = Response::error('Error');

        $reflection = new \ReflectionClass($response);
        $property = $reflection->getProperty('headers');
        $property->setAccessible(true);

        $headers = $property->getValue($response);

        $this->assertEquals('application/json', $headers['Content-Type']);
    }

    public function testSuccessWithComplexData(): void
    {
        $data = [
            'users' => [
                ['id' => 1, 'name' => 'John'],
                ['id' => 2, 'name' => 'Jane'],
            ],
            'total' => 2,
        ];

        $response = Response::success($data);

        $reflection = new \ReflectionClass($response);
        $property = $reflection->getProperty('content');
        $property->setAccessible(true);

        $content = json_decode($property->getValue($response), true);

        $this->assertEquals($data, $content['data']);
    }
}
