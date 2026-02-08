<?php

declare(strict_types=1);

namespace Tests\Unit\Presentation\Controllers;

use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Presentation\Controllers\Controller;
use App\Presentation\Interfaces\ActionInterface;
use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    public function testHandleRequestReturnsResponseFromMatchingAction(): void
    {
        $request = new Request([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/test',
        ]);

        $expectedResponse = Response::success(['test' => 'data']);

        $matchingAction = $this->createMock(ActionInterface::class);
        $matchingAction->method('supports')->willReturn(true);
        $matchingAction->method('handle')->willReturn($expectedResponse);

        $controller = new Controller([$matchingAction]);
        $response = $controller->handleRequest($request);

        $this->assertSame($expectedResponse, $response);
    }

    public function testHandleRequestReturns404(): void
    {
        $request = new Request([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/unknown',
        ]);

        $action = $this->createMock(ActionInterface::class);
        $action->method('supports')->willReturn(false);

        $controller = new Controller([$action]);
        $response = $controller->handleRequest($request);

        $reflection = new \ReflectionClass($response);
        $statusCodeProperty = $reflection->getProperty('statusCode');
        $statusCodeProperty->setAccessible(true);

        $this->assertEquals(404, $statusCodeProperty->getValue($response));
    }

    public function testHandleRequestWithEmptyActions(): void
    {
        $request = new Request([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/test',
        ]);

        $controller = new Controller([]);
        $response = $controller->handleRequest($request);

        $reflection = new \ReflectionClass($response);
        $statusCodeProperty = $reflection->getProperty('statusCode');
        $statusCodeProperty->setAccessible(true);

        $this->assertEquals(404, $statusCodeProperty->getValue($response));
    }
}
