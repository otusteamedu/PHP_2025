<?php

declare(strict_types=1);

namespace MkdBot\Tests\Integration\Persistence;

use MkdBot\Domain\Entity\News;
use MkdBot\Domain\Entity\NewsDelivery;
use MkdBot\Domain\Enum\NewsDeliveryStatus;
use MkdBot\Domain\Enum\NewsStatus;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Domain\Interface\NewsDeliveryRepositoryInterface;
use MkdBot\Domain\Interface\NewsRepositoryInterface;
use MkdBot\Infrastructure\Persistence\PostgresNewsDeliveryRepository;
use MkdBot\Infrastructure\Persistence\PostgresNewsRepository;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;

/**
 * Интеграционный тест PostgresNewsDeliveryRepository
 * Требует запущенную БД Postgres — пропускается если БД недоступна
 */
class PostgresNewsDeliveryRepositoryTest extends TestCase
{
    private ?NewsDeliveryRepositoryInterface $repository = null;
    private ?NewsRepositoryInterface $newsRepo = null;
    private ?PDO $pdo = null;

    protected function setUp(): void
    {
        $host = getenv('PG_HOST') ?: 'postgres';
        $port = getenv('PG_PORT') ?: '5432';
        $db = getenv('PG_DB') ?: 'mkd_bot';
        $user = getenv('PG_USER') ?: 'mkd_user';
        $password = getenv('PG_PASSWORD') ?: 'secret';

        try {
            $dsn = "pgsql:host={$host};port={$port};dbname={$db}";
            $this->pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            $dbConn = new class ($this->pdo) implements DatabaseConnectionInterface {
                public function __construct(private PDO $pdo)
                {
                }
                public function getConnection(): PDO
                {
                    return $this->pdo;
                }
                public function isAvailable(): bool
                {
                    return true;
                }
                public function reconnect(): PDO
                {
                    return $this->getConnection();
                }
                public function ensureConnection(): PDO
                {
                    return $this->getConnection();
                }
            };
            $this->repository = new PostgresNewsDeliveryRepository($dbConn);
            $this->newsRepo = new PostgresNewsRepository($dbConn);
        } catch (PDOException $e) {
            $this->markTestSkipped('Postgres недоступна: ' . $e->getMessage());
        }
    }

    public function testSaveAndFindDelivery(): void
    {
        if ($this->repository === null || $this->newsRepo === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        // Создаём новость для внешнего ключа
        $news = $this->newsRepo->save(new News(
            title: 'Для доставки ' . uniqid('', true),
            content: 'Контент',
            status: NewsStatus::Pending,
        ));

        $delivery = new NewsDelivery(
            newsId: $news->getId(),
            userId: 99999,
            status: NewsDeliveryStatus::Pending,
        );

        $saved = $this->repository->save($delivery);
        $found = $this->repository->findByNewsIdAndUserId($news->getId(), 99999);

        $this->assertNotNull($found);
        $this->assertEquals($news->getId(), $found->getNewsId());
        $this->assertEquals(99999, $found->getUserId());
        $this->assertEquals(NewsDeliveryStatus::Pending, $found->getStatus());
    }

    public function testFindByNewsIdAndUserIdNotFound(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $found = $this->repository->findByNewsIdAndUserId(999999, 999999);
        $this->assertNull($found);
    }

    public function testMarkAsSent(): void
    {
        if ($this->repository === null || $this->newsRepo === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $news = $this->newsRepo->save(new News(
            title: 'Для отправки ' . uniqid('', true),
            content: 'Контент',
            status: NewsStatus::Delivering,
        ));

        $delivery = new NewsDelivery(
            newsId: $news->getId(),
            userId: 88888,
            status: NewsDeliveryStatus::Pending,
        );
        $this->repository->save($delivery);

        $this->repository->markAsSent($news->getId(), 88888);

        $found = $this->repository->findByNewsIdAndUserId($news->getId(), 88888);
        $this->assertEquals(NewsDeliveryStatus::Sent, $found->getStatus());
        $this->assertNotNull($found->getDeliveredAt());
    }

    public function testMarkAsFailed(): void
    {
        if ($this->repository === null || $this->newsRepo === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $news = $this->newsRepo->save(new News(
            title: 'Для ошибки ' . uniqid('', true),
            content: 'Контент',
            status: NewsStatus::Delivering,
        ));

        $delivery = new NewsDelivery(
            newsId: $news->getId(),
            userId: 77777,
            status: NewsDeliveryStatus::Pending,
        );
        $this->repository->save($delivery);

        $this->repository->markAsFailed($news->getId(), 77777);

        $found = $this->repository->findByNewsIdAndUserId($news->getId(), 77777);
        $this->assertEquals(NewsDeliveryStatus::Failed, $found->getStatus());
    }

    public function testFindPendingByNewsId(): void
    {
        if ($this->repository === null || $this->newsRepo === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $news = $this->newsRepo->save(new News(
            title: 'Для pending-списка ' . uniqid('', true),
            content: 'Контент',
            status: NewsStatus::Delivering,
        ));

        $delivery1 = new NewsDelivery(newsId: $news->getId(), userId: 55551, status: NewsDeliveryStatus::Pending);
        $delivery2 = new NewsDelivery(newsId: $news->getId(), userId: 55552, status: NewsDeliveryStatus::Pending);
        $this->repository->save($delivery1);
        $this->repository->save($delivery2);

        $pending = $this->repository->findPendingByNewsId($news->getId());
        $this->assertGreaterThanOrEqual(2, count($pending));
    }

    public function testFindExistingDeliveryUserIdsReturnsEmptyForNewsWithoutDeliveries(): void
    {
        if ($this->repository === null || $this->newsRepo === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $news = $this->newsRepo->save(new News(
            title: 'Без доставок ' . uniqid('', true),
            content: 'Контент',
            status: NewsStatus::Pending,
        ));

        $result = $this->repository->findExistingDeliveryUserIds($news->getId());
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testFindExistingDeliveryUserIdsReturnsUserIdsForNewsWithDeliveries(): void
    {
        if ($this->repository === null || $this->newsRepo === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $news = $this->newsRepo->save(new News(
            title: 'С доставками ' . uniqid('', true),
            content: 'Контент',
            status: NewsStatus::Delivering,
        ));

        $delivery1 = new NewsDelivery(newsId: $news->getId(), userId: 44441, status: NewsDeliveryStatus::Pending);
        $delivery2 = new NewsDelivery(newsId: $news->getId(), userId: 44442, status: NewsDeliveryStatus::Pending);
        $this->repository->save($delivery1);
        $this->repository->save($delivery2);

        $result = $this->repository->findExistingDeliveryUserIds($news->getId());
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertContains(44441, $result);
        $this->assertContains(44442, $result);
    }

    public function testFindExistingDeliveryUserIdsReturnsDifferentArraysForDifferentNews(): void
    {
        if ($this->repository === null || $this->newsRepo === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $news1 = $this->newsRepo->save(new News(
            title: 'Новость 1 ' . uniqid('', true),
            content: 'Контент',
            status: NewsStatus::Delivering,
        ));
        $news2 = $this->newsRepo->save(new News(
            title: 'Новость 2 ' . uniqid('', true),
            content: 'Контент',
            status: NewsStatus::Delivering,
        ));

        $delivery1 = new NewsDelivery(newsId: $news1->getId(), userId: 33331, status: NewsDeliveryStatus::Pending);
        $delivery2 = new NewsDelivery(newsId: $news2->getId(), userId: 33332, status: NewsDeliveryStatus::Pending);
        $this->repository->save($delivery1);
        $this->repository->save($delivery2);

        $result1 = $this->repository->findExistingDeliveryUserIds($news1->getId());
        $result2 = $this->repository->findExistingDeliveryUserIds($news2->getId());

        $this->assertContains(33331, $result1);
        $this->assertNotContains(33332, $result1);
        $this->assertContains(33332, $result2);
        $this->assertNotContains(33331, $result2);
    }
}
