<?php

declare(strict_types=1);

namespace MkdBot\Application\UseCase;

use MkdBot\Application\DTO\MaxBotEventDTO;
use MkdBot\Domain\Interface\BotSubscriberRepositoryInterface;
use MkdBot\Domain\Interface\ConversationStateRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Обработка события bot_stopped — очистка состояния диалога и деактивация подписчика
 */
class HandleBotStopped
{
    public function __construct(
        private readonly ConversationStateRepositoryInterface $stateRepo,
        private readonly LoggerInterface $logger,
        private readonly BotSubscriberRepositoryInterface $subscriberRepo,
    ) {
    }

    /**
     * Очищает состояние диалога при остановке бота пользователем
     */
    public function execute(MaxBotEventDTO $dto): void
    {
        $this->logger->info("bot_stopped: userId={$dto->userId}");
        $this->stateRepo->deleteByUserId($dto->userId);

        $this->subscriberRepo->markAsInactive($dto->userId);
        $this->logger->info("Подписчик деактивирован: userId={$dto->userId}");
    }
}
