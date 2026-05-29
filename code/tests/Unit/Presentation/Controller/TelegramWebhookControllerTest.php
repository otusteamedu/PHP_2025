<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Presentation\Controller;

use MkdBot\Application\UseCase\HandleTelegramWebhook;
use MkdBot\Presentation\Controller\TelegramWebhookController;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Slim\Psr7\Response;

/**
 * Юнит-тесты для TelegramWebhookController — endpoint /webhook/telegram
 */
class TelegramWebhookControllerTest extends TestCase
{
    private HandleTelegramWebhook $handleTelegramWebhook;
    private TelegramWebhookController $controller;

    protected function setUp(): void
    {
        $this->handleTelegramWebhook = $this->createMock(HandleTelegramWebhook::class);
        $this->controller = new TelegramWebhookController($this->handleTelegramWebhook);
    }

    /**
     * Получение webhook — вызов HandleTelegramWebhook::execute() с телом запроса + возврат 200 OK
     */
    public function testHandleCallsExecuteAndReturns200(): void
    {
        $requestBody = '{"update_id":123,"message":{"text":"hello"}}';

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn($requestBody);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn($stream);

        // Ожидаем вызов execute с телом запроса
        $this->handleTelegramWebhook->expects($this->once())
            ->method('execute')
            ->with($requestBody);

        $response = new Response();
        $result = $this->controller->handle($request, $response);

        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * Пустое тело запроса — всё равно 200 OK (v1: минимальный контроллер)
     */
    public function testHandleWithEmptyBodyReturns200(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('');

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn($stream);

        // Ожидаем вызов execute с пустой строкой
        $this->handleTelegramWebhook->expects($this->once())
            ->method('execute')
            ->with('');

        $response = new Response();
        $result = $this->controller->handle($request, $response);

        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * Webhook с JSON-телом — execute получает корректное тело
     */
    public function testHandlePassesJsonBodyToUseCase(): void
    {
        $requestBody = json_encode([
            'update_id' => 999,
            'message' => [
                'message_id' => 1,
                'text' => '/start',
                'chat' => ['id' => 42],
            ],
        ]);

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn($requestBody);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn($stream);

        $this->handleTelegramWebhook->expects($this->once())
            ->method('execute')
            ->with($requestBody);

        $response = new Response();
        $result = $this->controller->handle($request, $response);

        $this->assertSame(200, $result->getStatusCode());
    }
}
