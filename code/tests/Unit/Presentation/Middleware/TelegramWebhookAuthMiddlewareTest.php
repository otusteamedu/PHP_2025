<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Presentation\Middleware;

use MkdBot\Presentation\Middleware\TelegramWebhookAuthMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Response;

class TelegramWebhookAuthMiddlewareTest extends TestCase
{
    public function testAllowsRequestWithCorrectToken(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new Response(200));

        $middleware = new TelegramWebhookAuthMiddleware('tg-token', $logger);
        $request = (new RequestFactory())->createRequest('POST', '/webhook/telegram');
        $request = $request->withHeader('X-Telegram-Bot-Api-Secret-Token', 'tg-token');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testBlocksWithWrongToken(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning');
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $middleware = new TelegramWebhookAuthMiddleware('tg-token', $logger);
        $request = (new RequestFactory())->createRequest('POST', '/webhook/telegram');
        $request = $request->withHeader('X-Telegram-Bot-Api-Secret-Token', 'wrong-token');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testBlocksWithoutToken(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning');
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $middleware = new TelegramWebhookAuthMiddleware('tg-token', $logger);
        $request = (new RequestFactory())->createRequest('POST', '/webhook/telegram');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testAllowsRequestWhenSecretIsNull(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new Response(200));

        $middleware = new TelegramWebhookAuthMiddleware(null, $logger);
        $request = (new RequestFactory())->createRequest('POST', '/webhook/telegram');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testAllowsRequestWhenSecretIsEmptyString(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new Response(200));

        $middleware = new TelegramWebhookAuthMiddleware('', $logger);
        $request = (new RequestFactory())->createRequest('POST', '/webhook/telegram');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
