<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Application\UseCase;

use MkdBot\Application\DTO\MaxBotEventDTO;
use MkdBot\Application\Service\MainMenuSender;
use MkdBot\Application\UseCase\HandleBotStarted;
use MkdBot\Domain\Interface\BotSubscriberRepositoryInterface;
use MkdBot\Domain\Interface\ConversationStateRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class HandleBotStartedTest extends TestCase
{
    public function testShowsMainMenuAndDeletesState(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $subscriberRepo = $this->createMock(BotSubscriberRepositoryInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        // Ожидаем удаление предыдущего состояния
        $stateRepo->expects($this->once())->method('deleteByUserId')->with(67890);

        // Ожидаем отправку главного меню через MainMenuSender
        $mainMenuSender->expects($this->once())->method('send')->with(67890);

        // Новый подписчик — findByUserId возвращает null, ожидаем save
        $subscriberRepo->method('findByUserId')->with(67890)->willReturn(null);
        $subscriberRepo->expects($this->once())->method('save');

        $logger->method('info');

        $useCase = new HandleBotStarted($stateRepo, $logger, $subscriberRepo, $mainMenuSender);

        $dto = new MaxBotEventDTO(chatId: 12345, userId: 67890, userName: 'Test', eventType: 'bot_started');
        $useCase->execute($dto);
    }

    public function testReactivatesExistingSubscriber(): void
    {
        $stateRepo = $this->createMock(ConversationStateRepositoryInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $subscriberRepo = $this->createMock(BotSubscriberRepositoryInterface::class);
        $mainMenuSender = $this->createMock(MainMenuSender::class);

        $stateRepo->method('deleteByUserId');
        $mainMenuSender->method('send');

        // Существующий подписчик — findByUserId возвращает объект, ожидаем markAsActive
        $existingSubscriber = $this->createMock(\MkdBot\Domain\Entity\BotSubscriber::class);
        $subscriberRepo->method('findByUserId')->with(67890)->willReturn($existingSubscriber);
        $subscriberRepo->expects($this->once())->method('markAsActive')->with(67890);
        $subscriberRepo->expects($this->never())->method('save');

        $logger->method('info');

        $useCase = new HandleBotStarted($stateRepo, $logger, $subscriberRepo, $mainMenuSender);

        $dto = new MaxBotEventDTO(chatId: 12345, userId: 67890, userName: 'Test', eventType: 'bot_started');
        $useCase->execute($dto);
    }
}
