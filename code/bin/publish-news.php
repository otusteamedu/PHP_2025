<?php

declare(strict_types=1);

/**
 * Скрипт добавления и публикации новости
 * Аргументы: --title="Заголовок" --content="Текст новости"
 *
 * Создаёт запись в news со статусом pending,
 * генерирует записи в news_deliveries для всех активных подписчиков,
 * публикует сообщения в RabbitMQ очередь mkd.news.delivery,
 * обновляет статус новости на delivering
 */

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/bootstrap.php';

use DI\ContainerBuilder;
use MkdBot\Application\DTO\NewsDeliveryDTO;
use MkdBot\Domain\Entity\News;
use MkdBot\Domain\Entity\NewsDelivery;
use MkdBot\Domain\Interface\BotSubscriberRepositoryInterface;
use MkdBot\Domain\Interface\NewsDeliveryRepositoryInterface;
use MkdBot\Domain\Interface\NewsRepositoryInterface;
use MkdBot\Domain\Interface\QueuePublisherInterface;
use MkdBot\Domain\Enum\NewsDeliveryStatus;
use MkdBot\Domain\Enum\NewsStatus;

$options = getopt('', ['title:', 'content:']);
$title = $options['title'] ?? null;
$content = $options['content'] ?? null;

if ($title === null || $content === null) {
    echo "Использование: php bin/publish-news.php --title=\"Заголовок\" --content=\"Текст новости\"\n";
    exit(1);
}

try {
    $containerBuilder = new ContainerBuilder();
    $containerBuilder->addDefinitions(require __DIR__ . '/../config/dependencies.php');
    $container = $containerBuilder->build();

    $newsRepo = $container->get(NewsRepositoryInterface::class);
    $deliveryRepo = $container->get(NewsDeliveryRepositoryInterface::class);
    $subscriberRepo = $container->get(BotSubscriberRepositoryInterface::class);
    $queuePublisher = $container->get(QueuePublisherInterface::class);

    $news = new News(title: $title, content: $content, status: NewsStatus::Pending);
    $news = $newsRepo->save($news);
    $newsId = $news->getId();

    echo "Новость создана: id={$newsId}, title=\"{$title}\"\n";

    $subscribers = $subscriberRepo->findActive();

    if (empty($subscribers)) {
        echo "Нет активных подписчиков — новость останется в статусе pending\n";
        exit(0);
    }

    $publishedCount = 0;

    foreach ($subscribers as $subscriber) {
        $userId = $subscriber->getUserId();

        $delivery = new NewsDelivery(
            newsId: $newsId,
            userId: $userId,
            status: NewsDeliveryStatus::Pending,
        );
        $deliveryRepo->save($delivery);

        $dto = new NewsDeliveryDTO(
            newsId: $newsId,
            userId: $userId,
            title: $title,
            content: $content,
        );
        $queuePublisher->publish('mkd.news.delivery', $dto->toArray());
        $publishedCount++;
    }

    $newsRepo->markAsDelivering($newsId);

    echo "Новость id={$newsId} опубликована для {$publishedCount} подписчиков\n";
} catch (\Throwable $e) {
    echo "Ошибка публикации новости: " . $e->getMessage() . "\n";
    exit(1);
}
