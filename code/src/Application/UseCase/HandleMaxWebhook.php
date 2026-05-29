<?php

declare(strict_types=1);

namespace MkdBot\Application\UseCase;

use MkdBot\Application\DTO\MaxBotEventDTO;
use MkdBot\Application\DTO\MaxCallbackDTO;
use MkdBot\Application\DTO\MaxMessageDTO;
use Psr\Log\LoggerInterface;

/**
 * Маршрутизатор webhook-событий от Max — делегирует специализированным Use Cases
 */
class HandleMaxWebhook
{
    public function __construct(
        private readonly HandleChannelMessage $handleChannelMessage,
        private readonly HandleDialogMessage $handleDialogMessage,
        private readonly HandleMessageCallback $handleMessageCallback,
        private readonly HandleBotStarted $handleBotStarted,
        private readonly HandleBotStopped $handleBotStopped,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Маршрутизирует DTO по типу к соответствующему обработчику
     */
    public function execute(MaxMessageDTO|MaxCallbackDTO|MaxBotEventDTO $dto): void
    {
        match (true) {
            $dto instanceof MaxMessageDTO => $this->routeMessage($dto),
            $dto instanceof MaxCallbackDTO => $this->handleMessageCallback->execute($dto),
            default => $this->routeBotEvent($dto),
        };
    }

    /**
     * Маршрутизирует текстовое сообщение: канал -> HandleChannelMessage, диалог -> HandleDialogMessage
     */
    private function routeMessage(MaxMessageDTO $dto): void
    {
        if ($dto->chatType === 'channel') {
            $this->logger->info("Маршрутизация: сообщение из канала -> дублирование в Telegram, mid={$dto->mid}");
            $this->handleChannelMessage->execute($dto);
        } else {
            $this->handleDialogMessage->execute($dto);
        }
    }

    /**
     * Маршрутизирует события бота (bot_started / bot_stopped)
     */
    private function routeBotEvent(MaxBotEventDTO $dto): void
    {
        match ($dto->eventType) {
            'bot_started' => $this->handleBotStarted->execute($dto),
            'bot_stopped' => $this->handleBotStopped->execute($dto),
            default => $this->logger->warning('Неизвестное событие бота: ' . $dto->eventType),
        };
    }
}
