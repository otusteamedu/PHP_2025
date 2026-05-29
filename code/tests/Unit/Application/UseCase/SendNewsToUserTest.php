<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Application\UseCase;

use MkdBot\Application\DTO\NewsDeliveryDTO;
use MkdBot\Application\UseCase\SendNewsToUser;
use MkdBot\Domain\Interface\MaxBotClientInterface;
use MkdBot\Domain\Interface\NewsDeliveryRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Тесты Use Case SendNewsToUser — отправка новости конкретному пользователю
 */
class SendNewsToUserTest extends TestCase
{
    public function testSendNewsSuccessfully(): void
    {
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $deliveryRepo = $this->createMock(NewsDeliveryRepositoryInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $dto = new NewsDeliveryDTO(
            newsId: 1,
            userId: 12345,
            title: 'Заголовок',
            content: 'Текст новости',
        );

        // Ожидаем отправку сообщения пользователю
        $maxBot->expects($this->once())
            ->method('sendMessageToUser')
            ->with(12345, $this->stringContains('Заголовок'));

        // Ожидаем пометку доставки как отправленной
        $deliveryRepo->expects($this->once())
            ->method('markAsSent')
            ->with(1, 12345);

        $logger->method('info');

        $useCase = new SendNewsToUser($maxBot, $deliveryRepo, $logger);
        $useCase->execute($dto);
    }

    public function testSendNewsFailureMarksAsFailed(): void
    {
        $maxBot = $this->createMock(MaxBotClientInterface::class);
        $deliveryRepo = $this->createMock(NewsDeliveryRepositoryInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $dto = new NewsDeliveryDTO(
            newsId: 2,
            userId: 67890,
            title: 'Ошибка',
            content: 'Не отправится',
        );

        // Симулируем ошибку отправки
        $maxBot->method('sendMessageToUser')
            ->willThrowException(new RuntimeException('Сетевая ошибка'));

        // Ожидаем пометку доставки как неудачной
        $deliveryRepo->expects($this->once())
            ->method('markAsFailed')
            ->with(2, 67890);

        $logger->method('info');
        $logger->method('error');

        $useCase = new SendNewsToUser($maxBot, $deliveryRepo, $logger);

        // Ожидаем, что исключение пробрасывается дальше (для NACK в consumer)
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Сетевая ошибка');

        $useCase->execute($dto);
    }
}
