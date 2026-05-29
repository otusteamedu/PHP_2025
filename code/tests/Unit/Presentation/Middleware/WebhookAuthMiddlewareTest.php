<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Presentation\Middleware;

use MkdBot\Presentation\Middleware\WebhookAuthMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Response;

class WebhookAuthMiddlewareTest extends TestCase
{
    public function testAllowsMaxWebhookWithCorrectSecret(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new Response(200));

        $middleware = new WebhookAuthMiddleware('my-secret', null, $logger);

        $request = (new RequestFactory())->createRequest('POST', '/webhook/max');
        $request = $request->withHeader('X-Max-Bot-Api-Secret', 'my-secret');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testBlocksMaxWebhookWithWrongSecret(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $middleware = new WebhookAuthMiddleware('my-secret', null, $logger);

        $request = (new RequestFactory())->createRequest('POST', '/webhook/max');
        $request = $request->withHeader('X-Max-Bot-Api-Secret', 'wrong-secret');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testBlocksMaxWebhookWithoutSecret(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $middleware = new WebhookAuthMiddleware('my-secret', null, $logger);

        $request = (new RequestFactory())->createRequest('POST', '/webhook/max');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testAllowsTelegramWebhookWithoutSecret(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new Response(200));

        // telegramSecretToken = null — проверка не требуется
        $middleware = new WebhookAuthMiddleware('max-secret', null, $logger);

        $request = (new RequestFactory())->createRequest('POST', '/webhook/telegram');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testAllowsTelegramWebhookWithCorrectToken(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new Response(200));

        $middleware = new WebhookAuthMiddleware('max-secret', 'tg-token', $logger);

        $request = (new RequestFactory())->createRequest('POST', '/webhook/telegram');
        $request = $request->withHeader('X-Telegram-Bot-Api-Secret-Token', 'tg-token');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testBlocksTelegramWebhookWithWrongToken(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $middleware = new WebhookAuthMiddleware('max-secret', 'tg-token', $logger);

        $request = (new RequestFactory())->createRequest('POST', '/webhook/telegram');
        $request = $request->withHeader('X-Telegram-Bot-Api-Secret-Token', 'wrong-token');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testAllowsHealthWithoutSecret(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new Response(200));

        $middleware = new WebhookAuthMiddleware('max-secret', 'tg-token', $logger);

        $request = (new RequestFactory())->createRequest('GET', '/health');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testAllowsOtherRoutesWithoutSecret(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new Response(200));

        $middleware = new WebhookAuthMiddleware('max-secret', 'tg-token', $logger);

        $request = (new RequestFactory())->createRequest('GET', '/some/other/path');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testAllowsMaxWebhookSubpathWithCorrectSecret(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new Response(200));

        $middleware = new WebhookAuthMiddleware('my-secret', null, $logger);

        $request = (new RequestFactory())->createRequest('POST', '/webhook/max/');
        $request = $request->withHeader('X-Max-Bot-Api-Secret', 'my-secret');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testBlocksMaxWebhookSubpathWithWrongSecret(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $middleware = new WebhookAuthMiddleware('my-secret', null, $logger);

        $request = (new RequestFactory())->createRequest('POST', '/webhook/max/');
        $request = $request->withHeader('X-Max-Bot-Api-Secret', 'wrong-secret');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testSkipsMaxWebhookPrefixedPathMaxFoo(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new Response(200));

        $middleware = new WebhookAuthMiddleware('my-secret', null, $logger);

        // /webhook/max-foo should NOT match the webhook auth check
        $request = (new RequestFactory())->createRequest('POST', '/webhook/max-foo');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSkipsMaxWebhookPrefixedPathMaximize(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new Response(200));

        $middleware = new WebhookAuthMiddleware('my-secret', null, $logger);

        // /webhook/maximize should NOT match the webhook auth check
        $request = (new RequestFactory())->createRequest('POST', '/webhook/maximize');

        $response = $middleware->process($request, $handler);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
