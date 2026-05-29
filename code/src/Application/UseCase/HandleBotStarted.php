<?php

declare(strict_types=1);

namespace MkdBot\Application\UseCase;

use MkdBot\Application\DTO\MaxBotEventDTO;
use MkdBot\Application\Service\MainMenuSender;
use MkdBot\Domain\Entity\BotSubscriber;
use MkdBot\Domain\Interface\BotSubscriberRepositoryInterface;
use MkdBot\Domain\Interface\ConversationStateRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Обработка события bot_started — показ главного меню с Inline Keyboard
 * Важно: BotStartedUpdate::getChatId() возвращает ID диалога, а не чата.
 * Для отправки ответа используем sendMessageToUser($userId, ...)
 */
class HandleBotStarted
{
    public function __construct(
        private readonly ConversationStateRepositoryInterface $stateRepo,
        private readonly LoggerInterface $logger,
        private readonly BotSubscriberRepositoryInterface $subscriberRepo,
        private readonly MainMenuSender $mainMenuSender,
    ) {
    }

    /**
     * Показывает главное меню при нажатии кнопки «Start»
     */
    public function execute(MaxBotEventDTO $dto): void
    {
        $this->logger->info("bot_started: userId={$dto->userId}");

        $this->stateRepo->deleteByUserId($dto->userId);

        $this->mainMenuSender->send($dto->userId);

        // Добавляем или активируем подписчика
        $existingSubscriber = $this->subscriberRepo->findByUserId($dto->userId);
        if ($existingSubscriber !== null) {
            $this->subscriberRepo->markAsActive($dto->userId);
            $this->logger->info("Подписчик активирован: userId={$dto->userId}");
        } else {
            $subscriber = new BotSubscriber(
                userId: $dto->userId,
                userName: $dto->userName,
            );
            $this->subscriberRepo->save($subscriber);
            $this->logger->info("Подписчик зарегистрирован: userId={$dto->userId}");
        }
    }
}
