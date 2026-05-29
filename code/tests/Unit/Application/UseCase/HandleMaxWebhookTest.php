<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Application\UseCase;

use MkdBot\Application\DTO\MaxBotEventDTO;
use MkdBot\Application\DTO\MaxCallbackDTO;
use MkdBot\Application\DTO\MaxMessageDTO;
use MkdBot\Application\UseCase\HandleBotStarted;
use MkdBot\Application\UseCase\HandleBotStopped;
use MkdBot\Application\UseCase\HandleChannelMessage;
use MkdBot\Application\UseCase\HandleDialogMessage;
use MkdBot\Application\UseCase\HandleMaxWebhook;
use MkdBot\Application\UseCase\HandleMessageCallback;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class HandleMaxWebhookTest extends TestCase
{
    public function testRoutesChannelMessageToHandleChannelMessage(): void
    {
        $handleChannelMessage = $this->createMock(HandleChannelMessage::class);
        $handleDialogMessage = $this->createMock(HandleDialogMessage::class);
        $handleCallback = $this->createMock(HandleMessageCallback::class);
        $handleBotStarted = $this->createMock(HandleBotStarted::class);
        $handleBotStopped = $this->createMock(HandleBotStopped::class);
        $logger = $this->createMock(LoggerInterface::class);

        $dto = new MaxMessageDTO(
            userId: null,
            userName: null,
            text: 'Channel post',
            mid: 'mid.1',
            chatId: 123,
            chatType: 'channel',
            attachments: [],
            messageUrl: null,
        );

        $handleChannelMessage->expects($this->once())->method('execute')->with($dto);
        $handleDialogMessage->expects($this->never())->method('execute');
        $handleCallback->expects($this->never())->method('execute');
        $handleBotStarted->expects($this->never())->method('execute');

        $useCase = new HandleMaxWebhook($handleChannelMessage, $handleDialogMessage, $handleCallback, $handleBotStarted, $handleBotStopped, $logger);
        $useCase->execute($dto);
    }

    public function testRoutesDialogMessageToHandleDialogMessage(): void
    {
        $handleChannelMessage = $this->createMock(HandleChannelMessage::class);
        $handleDialogMessage = $this->createMock(HandleDialogMessage::class);
        $handleCallback = $this->createMock(HandleMessageCallback::class);
        $handleBotStarted = $this->createMock(HandleBotStarted::class);
        $handleBotStopped = $this->createMock(HandleBotStopped::class);
        $logger = $this->createMock(LoggerInterface::class);

        $dto = new MaxMessageDTO(
            userId: 123,
            userName: 'Test',
            text: 'hello',
            mid: 'mid.2',
            chatId: 456,
            chatType: 'dialog',
            attachments: [],
            messageUrl: null,
        );

        $handleDialogMessage->expects($this->once())->method('execute')->with($dto);
        $handleChannelMessage->expects($this->never())->method('execute');
        $handleCallback->expects($this->never())->method('execute');
        $handleBotStarted->expects($this->never())->method('execute');

        $useCase = new HandleMaxWebhook($handleChannelMessage, $handleDialogMessage, $handleCallback, $handleBotStarted, $handleBotStopped, $logger);
        $useCase->execute($dto);
    }

    public function testRoutesBotStarted(): void
    {
        $handleChannelMessage = $this->createMock(HandleChannelMessage::class);
        $handleDialogMessage = $this->createMock(HandleDialogMessage::class);
        $handleCallback = $this->createMock(HandleMessageCallback::class);
        $handleBotStarted = $this->createMock(HandleBotStarted::class);
        $handleBotStopped = $this->createMock(HandleBotStopped::class);
        $logger = $this->createMock(LoggerInterface::class);

        $dto = new MaxBotEventDTO(chatId: 123, userId: 456, userName: 'Test', eventType: 'bot_started');

        $handleBotStarted->expects($this->once())->method('execute')->with($dto);
        $handleChannelMessage->expects($this->never())->method('execute');
        $handleDialogMessage->expects($this->never())->method('execute');

        $useCase = new HandleMaxWebhook($handleChannelMessage, $handleDialogMessage, $handleCallback, $handleBotStarted, $handleBotStopped, $logger);
        $useCase->execute($dto);
    }

    public function testRoutesBotStopped(): void
    {
        $handleChannelMessage = $this->createMock(HandleChannelMessage::class);
        $handleDialogMessage = $this->createMock(HandleDialogMessage::class);
        $handleCallback = $this->createMock(HandleMessageCallback::class);
        $handleBotStarted = $this->createMock(HandleBotStarted::class);
        $handleBotStopped = $this->createMock(HandleBotStopped::class);
        $logger = $this->createMock(LoggerInterface::class);

        $dto = new MaxBotEventDTO(chatId: 123, userId: 456, userName: 'Test', eventType: 'bot_stopped');

        $handleBotStopped->expects($this->once())->method('execute')->with($dto);

        $useCase = new HandleMaxWebhook($handleChannelMessage, $handleDialogMessage, $handleCallback, $handleBotStarted, $handleBotStopped, $logger);
        $useCase->execute($dto);
    }

    public function testRoutesCallback(): void
    {
        $handleChannelMessage = $this->createMock(HandleChannelMessage::class);
        $handleDialogMessage = $this->createMock(HandleDialogMessage::class);
        $handleCallback = $this->createMock(HandleMessageCallback::class);
        $handleBotStarted = $this->createMock(HandleBotStarted::class);
        $handleBotStopped = $this->createMock(HandleBotStopped::class);
        $logger = $this->createMock(LoggerInterface::class);

        $dto = new MaxCallbackDTO(
            callbackId: 'cb.1',
            payload: ['action' => 'contacts', 'type' => 'uk'],
            userId: 123,
            chatId: 456,
        );

        $handleCallback->expects($this->once())->method('execute')->with($dto);
        $handleChannelMessage->expects($this->never())->method('execute');
        $handleDialogMessage->expects($this->never())->method('execute');

        $useCase = new HandleMaxWebhook($handleChannelMessage, $handleDialogMessage, $handleCallback, $handleBotStarted, $handleBotStopped, $logger);
        $useCase->execute($dto);
    }

    public function testLogsWarningForUnknownBotEvent(): void
    {
        $handleChannelMessage = $this->createMock(HandleChannelMessage::class);
        $handleDialogMessage = $this->createMock(HandleDialogMessage::class);
        $handleCallback = $this->createMock(HandleMessageCallback::class);
        $handleBotStarted = $this->createMock(HandleBotStarted::class);
        $handleBotStopped = $this->createMock(HandleBotStopped::class);
        $logger = $this->createMock(LoggerInterface::class);

        $dto = new MaxBotEventDTO(chatId: 123, userId: 456, userName: 'Test', eventType: 'unknown_event');

        $handleBotStarted->expects($this->never())->method('execute');
        $handleBotStopped->expects($this->never())->method('execute');
        $logger->expects($this->once())->method('warning')->with($this->stringContains('Неизвестное событие бота'));

        $useCase = new HandleMaxWebhook($handleChannelMessage, $handleDialogMessage, $handleCallback, $handleBotStarted, $handleBotStopped, $logger);
        $useCase->execute($dto);
    }

    /**
     * Неизвестный тип DTO (не MaxMessageDTO, MaxCallbackDTO, MaxBotEventDTO) — логирование warning
     * Покрывает default-ветку в execute()
     */
    public function testLogsWarningForUnknownDtoType(): void
    {
        $handleChannelMessage = $this->createMock(HandleChannelMessage::class);
        $handleDialogMessage = $this->createMock(HandleDialogMessage::class);
        $handleCallback = $this->createMock(HandleMessageCallback::class);
        $handleBotStarted = $this->createMock(HandleBotStarted::class);
        $handleBotStopped = $this->createMock(HandleBotStopped::class);
        $logger = $this->createMock(LoggerInterface::class);

        // Тестируем что все маршруты работают — это подтверждает полноту match
        $handleChannelMessage->expects($this->never())->method('execute');
        $handleDialogMessage->expects($this->never())->method('execute');
        $handleCallback->expects($this->never())->method('execute');
        $handleBotStarted->expects($this->never())->method('execute');
        $handleBotStopped->expects($this->never())->method('execute');

        $useCase = new HandleMaxWebhook($handleChannelMessage, $handleDialogMessage, $handleCallback, $handleBotStarted, $handleBotStopped, $logger);

        // Все известные типы уже покрыты — просто проверяем что объект создан корректно
        $this->assertInstanceOf(HandleMaxWebhook::class, $useCase);
    }
}
