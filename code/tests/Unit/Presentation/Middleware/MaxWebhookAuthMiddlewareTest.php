<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Presentation\Middleware;

use MkdBot\Presentation\Middleware\MaxWebhookAuthMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Response;

class MaxWebhookAuthMiddlewareTest extends TestCase
{
    public function testAllowsRequestWithCorrectSecret(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new Response(200));

        $middleware = new MaxWebhookAuthMiddleware('my-secret', $logger);
        $request = (new RequestFactory())->createRequest('POST', '/webhook/max');
        $request = $request->withHeader('X-Max-Bot-Api-Secret', 'my-secret');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testBlocksWithWrongSecret(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning');
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $middleware = new MaxWebhookAuthMiddleware('my-secret', $logger);
        $request = (new RequestFactory())->createRequest('POST', '/webhook/max');
        $request = $request->withHeader('X-Max-Bot-Api-Secret', 'wrong-secret');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testBlocksWithoutSecret(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning');
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $middleware = new MaxWebhookAuthMiddleware('my-secret', $logger);
        $request = (new RequestFactory())->createRequest('POST', '/webhook/max');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testAllowsRequestWhenSecretIsEmpty(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new Response(200));

        $middleware = new MaxWebhookAuthMiddleware('', $logger);
        $request = (new RequestFactory())->createRequest('POST', '/webhook/max');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
