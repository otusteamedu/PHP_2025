<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Application\UseCase;

use MkdBot\Application\DTO\MaxBotEventDTO;
use MkdBot\Application\UseCase\HandleBotStopped;
use MkdBot\Domain\Interface\BotSubscriberRepositoryInterface;
use MkdBot\Domain\Interface\ConversationStateRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class HandleBotStoppedTest extends TestCase
{
    public function testDeletesConversationStateAndDeactivatesSubscriber(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $subscriberRepo = $this->createMock(BotSubscriberRepositoryInterface::class);

        // Ожидаем удаление состояния
        $stateRepo->expects($this->once())->method('deleteByUserId')->with(12345);

        // Ожидаем деактивацию подписчика
        $subscriberRepo->expects($this->once())->method('markAsInactive')->with(12345);

        $logger->method('info');

        $useCase = new HandleBotStopped($stateRepo, $logger, $subscriberRepo);

        $dto = new MaxBotEventDTO(chatId: 99999, userId: 12345, userName: 'Test', eventType: 'bot_stopped');
        $useCase->execute($dto);
    }
}
