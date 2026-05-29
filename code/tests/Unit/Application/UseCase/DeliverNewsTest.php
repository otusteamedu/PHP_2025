<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Application\UseCase;

use MkdBot\Application\UseCase\DeliverNews;
use MkdBot\Domain\Entity\BotSubscriber;
use MkdBot\Domain\Entity\News;
use MkdBot\Domain\Enum\NewsStatus;
use MkdBot\Domain\Enum\QueueNameType;
use MkdBot\Domain\Interface\BotSubscriberRepositoryInterface;
use MkdBot\Domain\Interface\NewsDeliveryRepositoryInterface;
use MkdBot\Domain\Interface\NewsRepositoryInterface;
use MkdBot\Domain\Interface\QueuePublisherInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Тесты Use Case DeliverNews — оркестрация рассылки новостей
 */
class DeliverNewsTest extends TestCase
{
    public function testNoPendingNews(): void
    {
        $newsRepo = $this->createMock(NewsRepositoryInterface::class);
        $deliveryRepo = $this->createMock(NewsDeliveryRepositoryInterface::class);
        $subscriberRepo = $this->createMock(BotSubscriberRepositoryInterface::class);
        $queuePublisher = $this->createMock(QueuePublisherInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $newsRepo->method('findPending')->willReturn([]);
        $subscriberRepo->method('findActive')->willReturn([]);
        $queuePublisher->expects($this->never())->method('publish');

        $logger->method('info');

        $useCase = new DeliverNews($newsRepo, $deliveryRepo, $subscriberRepo, $queuePublisher, $logger);
        $useCase->execute();

        // Если нет pending-новостей — публикация не вызывается
        $this->assertTrue(true); // Тест проходит без исключений
    }

    public function testNoActiveSubscribers(): void
    {
        $news = new News(id: 1, title: 'Тест', content: 'Контент', status: NewsStatus::Pending);

        $newsRepo = $this->createMock(NewsRepositoryInterface::class);
        $deliveryRepo = $this->createMock(NewsDeliveryRepositoryInterface::class);
        $subscriberRepo = $this->createMock(BotSubscriberRepositoryInterface::class);
        $queuePublisher = $this->createMock(QueuePublisherInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $newsRepo->method('findPending')->willReturn([$news]);
        $subscriberRepo->method('findActive')->willReturn([]);
        $queuePublisher->expects($this->never())->method('publish');

        $logger->method('info');

        $useCase = new DeliverNews($newsRepo, $deliveryRepo, $subscriberRepo, $queuePublisher, $logger);
        $useCase->execute();
    }

    public function testPublishesNewsToSubscribers(): void
    {
        $news = new News(id: 1, title: 'Новость', content: 'Текст новости', status: NewsStatus::Pending);
        $subscriber1 = new BotSubscriber(userId: 100, userName: 'Алиса');
        $subscriber2 = new BotSubscriber(userId: 200, userName: 'Борис');

        $newsRepo = $this->createMock(NewsRepositoryInterface::class);
        $deliveryRepo = $this->createMock(NewsDeliveryRepositoryInterface::class);
        $subscriberRepo = $this->createMock(BotSubscriberRepositoryInterface::class);
        $queuePublisher = $this->createMock(QueuePublisherInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $newsRepo->method('findPending')->willReturn([$news]);
        $subscriberRepo->method('findActive')->willReturn([$subscriber1, $subscriber2]);
        $deliveryRepo->method('findExistingDeliveryUserIds')->with(1)->willReturn([]);
        $deliveryRepo->method('save')->willReturnArgument(0);
        $newsRepo->expects($this->once())->method('markAsDelivering')->with(1);

        // Ожидаем 2 публикации в очередь
        $queuePublisher->expects($this->exactly(2))->method('publish')
            ->with(QueueNameType::NewsDelivery, $this->isType('array'));

        $logger->method('info');
        $logger->method('debug');

        $useCase = new DeliverNews($newsRepo, $deliveryRepo, $subscriberRepo, $queuePublisher, $logger);
        $useCase->execute();
    }

    public function testSkipsExistingDeliveries(): void
    {
        $news = new News(id: 1, title: 'Новость', content: 'Текст', status: NewsStatus::Pending);
        $subscriber = new BotSubscriber(userId: 100, userName: 'Алиса');
        $existingDelivery = $this->createMock(\MkdBot\Domain\Entity\NewsDelivery::class);

        $newsRepo = $this->createMock(NewsRepositoryInterface::class);
        $deliveryRepo = $this->createMock(NewsDeliveryRepositoryInterface::class);
        $subscriberRepo = $this->createMock(BotSubscriberRepositoryInterface::class);
        $queuePublisher = $this->createMock(QueuePublisherInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $newsRepo->method('findPending')->willReturn([$news]);
        $subscriberRepo->method('findActive')->willReturn([$subscriber]);
        $deliveryRepo->method('findExistingDeliveryUserIds')->with(1)->willReturn([100]);

        // Публикация не должна вызваться — доставка уже существует
        $queuePublisher->expects($this->never())->method('publish');

        $logger->method('info');
        $logger->method('debug');

        $useCase = new DeliverNews($newsRepo, $deliveryRepo, $subscriberRepo, $queuePublisher, $logger);
        $useCase->execute();
    }
}
