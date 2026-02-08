<?php

namespace Tests\Http;

use App\Http\Response;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use phpmock\mockery\PHPMockery;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testSendSetsHeadersAndStatusReturnsBody(): void
    {
        PHPMockery::mock('App\Http', 'http_response_code')
            ->once()
            ->with(201)
            ->andReturn(201);

        PHPMockery::mock('App\Http', 'header')
            ->once()
            ->with('Content-Type: application/json')
            ->andReturnNull();

        $result = Response::json(['test' => 1], 201)->send();

        $this->assertSame('{"test":1}', $result);
    }

    public function testViewSendReturnsFileAndSetsHtmlHeader(): void
    {
        $viewPath = \dirname(__DIR__) . '/Data/test.html';
        $this->assertFileExists($viewPath);

        PHPMockery::mock('App\Http', 'http_response_code')
            ->once()
            ->with(200)
            ->andReturn(200);

        PHPMockery::mock('App\Http', 'header')
            ->once()
            ->with('Content-Type: text/html')
            ->andReturnNull();

        $result = Response::view($viewPath)->send();

        $this->assertStringContainsString('<h1>test</h1>', $result);
    }
}
