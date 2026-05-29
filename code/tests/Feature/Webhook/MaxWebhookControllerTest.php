<?php

declare(strict_types=1);

namespace MkdBot\Tests\Feature\Webhook;

use MaxMessenger\Bot\MaxBot;
use MaxMessenger\Bot\Models\Responses\Update;
use MkdBot\Application\DTO\MaxBotEventDTO;
use MkdBot\Application\DTO\MaxCallbackDTO;
use MkdBot\Application\DTO\MaxMessageDTO;
use MkdBot\Application\UseCase\HandleMaxWebhook;
use MkdBot\Domain\Interface\ProcessedWebhookRepositoryInterface;
use MkdBot\Presentation\Controller\MaxWebhookController;
use MkdBot\Presentation\Mapper\MaxUpdateMapper;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Slim\Psr7\Response;

/**
 * Feature-тест MaxWebhookController
 * Тестирует обработку webhook-запросов с моками зависимостей
 */
class MaxWebhookControllerTest extends TestCase
{
    private MaxWebhookController $controller;
    private $handleMaxWebhook;
    private $processedWebhookRepo;
    private $mapper;
    private $logger;

    protected function setUp(): void
    {
        $this->mapper = $this->createMock(MaxUpdateMapper::class);
        $this->handleMaxWebhook = $this->createMock(HandleMaxWebhook::class);
        $this->processedWebhookRepo = $this->createMock(ProcessedWebhookRepositoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->logger->method('debug');
        $this->logger->method('info');
        $this->logger->method('warning');
        $this->logger->method('error');

        $this->controller = new MaxWebhookController(
            $this->mapper,
            $this->handleMaxWebhook,
            $this->processedWebhookRepo,
            $this->logger,
        );
    }

    /**
     * Создаёт PSR-7 запрос с заданным телом
     */
    private function createRequestWithBody(string $body): ServerRequestInterface
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $body);
        rewind($stream);
        $bodyStream = new \Slim\Psr7\Stream($stream);
        $request->method('getBody')->willReturn($bodyStream);

        return $request;
    }

    // --- Пустое тело и неверный JSON

    /**
     * Пустое тело — возврат 400
     */
    public function testEmptyBodyReturns400(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $body = new \Slim\Psr7\Stream(fopen('php://temp', 'r+'));
        $request->method('getBody')->willReturn($body);

        $response = $this->controller->handle($request, new Response());

        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * Неверный JSON — возврат 400
     */
    public function testInvalidJsonReturns400(): void
    {
        $request = $this->createRequestWithBody('not a json');

        $response = $this->controller->handle($request, new Response());

        $this->assertEquals(400, $response->getStatusCode());
    }

    // --- bot_started — вызов HandleMaxWebhook

    /**
     * Обработка bot_started — вызов HandleMaxWebhook
     */
    public function testValidBotStartedEventReturns200(): void
    {
        // JSON для bot_started события (формат Max API)
        $json = json_encode([
            'update_type' => 'bot_started',
            'timestamp' => time(),
            'chat_id' => 12345,
            'user' => ['user_id' => 67890, 'name' => 'Test'],
        ]);

        $request = $this->createRequestWithBody($json);

        // Маппер возвращает DTO
        $dto = new MaxBotEventDTO(
            chatId: 12345,
            userId: 67890,
            userName: 'Test',
            eventType: 'bot_started',
        );

        // Мокаем MaxBot::makeUpdateFromString через маппер
        // Контроллер вызывает MaxBot::makeUpdateFromString($body), затем $this->mapper->map($update)
        // Мы мокаем mapper->map чтобы вернуть DTO
        $this->mapper->method('map')->willReturn($dto);

        // HandleMaxWebhook ожидает вызов
        $this->handleMaxWebhook->expects($this->once())->method('execute')->with($dto);

        $response = $this->controller->handle($request, new Response());

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * bot_started — НЕ проверяет идемпотентность (tryAcquire не вызывается)
     */
    public function testBotStartedDoesNotCheckIdempotency(): void
    {
        $json = json_encode([
            'update_type' => 'bot_started',
            'timestamp' => time(),
            'chat_id' => 12345,
            'user' => ['user_id' => 67890, 'name' => 'Test'],
        ]);

        $request = $this->createRequestWithBody($json);

        $dto = new MaxBotEventDTO(
            chatId: 12345,
            userId: 67890,
            userName: 'Test',
            eventType: 'bot_started',
        );

        $this->mapper->method('map')->willReturn($dto);

        // tryAcquire НЕ должен вызываться для bot_started
        $this->processedWebhookRepo->expects($this->never())->method('tryAcquire');

        $this->handleMaxWebhook->method('execute');

        $this->controller->handle($request, new Response());
    }

    // --- message_created — вызов HandleMaxWebhook

    /**
     * Обработка message_created — вызов HandleMaxWebhook
     */
    public function testMessageCreatedCallsHandleMaxWebhook(): void
    {
        $json = json_encode([
            'update_type' => 'message_created',
            'timestamp' => time(),
        ]);

        $request = $this->createRequestWithBody($json);

        $dto = new MaxMessageDTO(
            userId: 67890,
            userName: 'Test User',
            text: 'Hello',
            mid: 'mid.abc123',
            chatId: 12345,
            chatType: 'dialog',
        );

        $this->mapper->method('map')->willReturn($dto);

        // Webhook ещё не обработан — tryAcquire возвращает true
        $this->processedWebhookRepo->method('tryAcquire')->willReturn(true);

        // HandleMaxWebhook ожидает вызов
        $this->handleMaxWebhook->expects($this->once())->method('execute')->with($dto);

        $response = $this->controller->handle($request, new Response());

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * message_created — проверяет идемпотентность через tryAcquire
     */
    public function testMessageCreatedChecksIdempotencyViaTryAcquire(): void
    {
        $json = json_encode([
            'update_type' => 'message_created',
            'timestamp' => time(),
        ]);

        $request = $this->createRequestWithBody($json);

        $dto = new MaxMessageDTO(
            userId: 67890,
            userName: 'Test User',
            text: 'Hello',
            mid: 'mid.abc123',
            chatId: 12345,
            chatType: 'dialog',
        );

        $this->mapper->method('map')->willReturn($dto);

        // tryAcquire вызывается с mid сообщения и возвращает true (webhook новый)
        $this->processedWebhookRepo->expects($this->once())
            ->method('tryAcquire')
            ->with('mid.abc123', 'max')
            ->willReturn(true);

        $this->handleMaxWebhook->method('execute');

        $this->controller->handle($request, new Response());
    }

    // --- message_callback — вызов HandleMaxWebhook

    /**
     * Обработка message_callback — вызов HandleMaxWebhook
     */
    public function testMessageCallbackCallsHandleMaxWebhook(): void
    {
        $json = json_encode([
            'update_type' => 'message_callback',
            'timestamp' => time(),
        ]);

        $request = $this->createRequestWithBody($json);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.123',
            payload: ['action' => 'confirm'],
            userId: 67890,
            chatId: 12345,
            userName: 'Test User',
        );

        $this->mapper->method('map')->willReturn($dto);

        // Webhook ещё не обработан — tryAcquire возвращает true
        $this->processedWebhookRepo->method('tryAcquire')->willReturn(true);

        // HandleMaxWebhook ожидает вызов
        $this->handleMaxWebhook->expects($this->once())->method('execute')->with($dto);

        $response = $this->controller->handle($request, new Response());

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * message_callback — проверяет идемпотентность с префиксом callback_ через tryAcquire
     */
    public function testMessageCallbackChecksIdempotencyWithCallbackPrefix(): void
    {
        $json = json_encode([
            'update_type' => 'message_callback',
            'timestamp' => time(),
        ]);

        $request = $this->createRequestWithBody($json);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.456',
            payload: ['action' => 'cancel'],
            userId: 67890,
            chatId: 12345,
        );

        $this->mapper->method('map')->willReturn($dto);

        // tryAcquire вызывается с 'callback_cb.456' для callback
        $this->processedWebhookRepo->expects($this->once())
            ->method('tryAcquire')
            ->with('callback_cb.456', 'max')
            ->willReturn(true);

        $this->handleMaxWebhook->method('execute');

        $this->controller->handle($request, new Response());
    }

    // --- Идемпотентность — повторный mid не обрабатывается

    /**
     * Идемпотентность — повторный mid не обрабатывается, возврат 200
     */
    public function testIdempotencyDuplicateMidReturns200WithoutProcessing(): void
    {
        $json = json_encode([
            'update_type' => 'message_created',
            'timestamp' => time(),
        ]);

        $request = $this->createRequestWithBody($json);

        $dto = new MaxMessageDTO(
            userId: 67890,
            userName: 'Test User',
            text: 'Hello',
            mid: 'mid.duplicate',
            chatId: 12345,
            chatType: 'dialog',
        );

        $this->mapper->method('map')->willReturn($dto);

        // tryAcquire возвращает false — webhook уже обработан
        $this->processedWebhookRepo->method('tryAcquire')->with('mid.duplicate', 'max')->willReturn(false);

        // HandleMaxWebhook НЕ должен вызываться
        $this->handleMaxWebhook->expects($this->never())->method('execute');

        $response = $this->controller->handle($request, new Response());

        $this->assertEquals(200, $response->getStatusCode());
    }

    // --- Неизвестный тип Update — возврат 200

    /**
     * Неизвестный тип Update (mapper возвращает null) — возврат 200
     */
    public function testUnknownUpdateTypeReturns200(): void
    {
        $json = json_encode([
            'update_type' => 'unknown_type',
            'timestamp' => time(),
        ]);

        $request = $this->createRequestWithBody($json);

        // Маппер возвращает null для неизвестного типа
        $this->mapper->method('map')->willReturn(null);

        // HandleMaxWebhook НЕ должен вызываться
        $this->handleMaxWebhook->expects($this->never())->method('execute');

        $response = $this->controller->handle($request, new Response());

        $this->assertEquals(200, $response->getStatusCode());
    }

    // --- Ошибка при обработке webhook

    /**
     * Ошибка при выполнении HandleMaxWebhook для bot_started — логирование и возврат 500
     * bot_started не проверяет идемпотентность, но при ошибке обработки — 500
     */
    public function testHandleMaxWebhookExceptionReturns500(): void
    {
        $json = json_encode([
            'update_type' => 'bot_started',
            'timestamp' => time(),
            'chat_id' => 12345,
            'user' => ['user_id' => 67890, 'name' => 'Test'],
        ]);

        $request = $this->createRequestWithBody($json);

        $dto = new MaxBotEventDTO(
            chatId: 12345,
            userId: 67890,
            userName: 'Test',
            eventType: 'bot_started',
        );

        $this->mapper->method('map')->willReturn($dto);

        // HandleMaxWebhook выбрасывает исключение
        $this->handleMaxWebhook->method('execute')->willThrowException(new RuntimeException('Processing error'));

        // Ожидаем логирование ошибки
        $this->logger->expects($this->once())->method('error')->with($this->stringContains('Ошибка обработки webhook'));

        // Контроллер возвращает 500 — Max API повторит доставку
        $response = $this->controller->handle($request, new Response());

        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * Ошибка при выполнении HandleMaxWebhook для message_created — возврат 500
     * Запись в processed_webhooks уже сделана в tryAcquire, повторная обработка не произойдёт
     */
    public function testHandleMaxWebhookExceptionForMessageReturns500(): void
    {
        $json = json_encode([
            'update_type' => 'message_created',
            'timestamp' => time(),
        ]);

        $request = $this->createRequestWithBody($json);

        $dto = new MaxMessageDTO(
            userId: 67890,
            userName: 'Test User',
            text: 'Hello',
            mid: 'mid.error123',
            chatId: 12345,
            chatType: 'dialog',
        );

        $this->mapper->method('map')->willReturn($dto);
        $this->processedWebhookRepo->method('tryAcquire')->willReturn(true);

        // HandleMaxWebhook выбрасывает исключение
        $this->handleMaxWebhook->method('execute')->willThrowException(new RuntimeException('Processing error'));

        // Контроллер возвращает 500 — Max API повторит доставку,
        // но повторная обработка не произойдёт (tryAcquire вернёт false)
        $response = $this->controller->handle($request, new Response());

        $this->assertEquals(500, $response->getStatusCode());
    }

    // --- message_callback с пустым callbackId — идемпотентность пропущена

    /**
     * Callback с пустым callbackId — идемпотентность НЕ проверяется (как bot_started)
     */
    public function testCallbackWithNullCallbackIdSkipsIdempotency(): void
    {
        $json = json_encode([
            'update_type' => 'message_callback',
            'timestamp' => time(),
        ]);

        $request = $this->createRequestWithBody($json);

        $dto = new MaxCallbackDTO(
            callbackId: null,
            payload: ['action' => 'contacts', 'type' => 'uk'],
            userId: 67890,
            chatId: 12345,
        );

        $this->mapper->method('map')->willReturn($dto);

        // tryAcquire НЕ должен вызываться для callback с пустым callbackId
        $this->processedWebhookRepo->expects($this->never())->method('tryAcquire');

        // HandleMaxWebhook всё равно вызывается
        $this->handleMaxWebhook->expects($this->once())->method('execute')->with($dto);

        $response = $this->controller->handle($request, new Response());

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Callback с пустой строкой callbackId — идемпотентность тоже НЕ проверяется
     */
    public function testCallbackWithEmptyStringCallbackIdSkipsIdempotency(): void
    {
        $json = json_encode([
            'update_type' => 'message_callback',
            'timestamp' => time(),
        ]);

        $request = $this->createRequestWithBody($json);

        $dto = new MaxCallbackDTO(
            callbackId: '',
            payload: ['action' => 'contacts', 'type' => 'uk'],
            userId: 67890,
            chatId: 12345,
        );

        $this->mapper->method('map')->willReturn($dto);

        // tryAcquire НЕ должен вызываться
        $this->processedWebhookRepo->expects($this->never())->method('tryAcquire');

        $this->handleMaxWebhook->expects($this->once())->method('execute')->with($dto);

        $response = $this->controller->handle($request, new Response());

        $this->assertEquals(200, $response->getStatusCode());
    }
}
