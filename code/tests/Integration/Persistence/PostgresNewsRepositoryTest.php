<?php

declare(strict_types=1);

namespace MkdBot\Tests\Integration\Persistence;

use MkdBot\Domain\Entity\News;
use MkdBot\Domain\Enum\NewsStatus;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Domain\Interface\NewsRepositoryInterface;
use MkdBot\Infrastructure\Persistence\PostgresNewsRepository;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;

/**
 * Интеграционный тест PostgresNewsRepository
 * Требует запущенную БД Postgres — пропускается если БД недоступна
 */
class PostgresNewsRepositoryTest extends TestCase
{
    private ?NewsRepositoryInterface $repository = null;
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
            $this->repository = new PostgresNewsRepository(
                new class ($this->pdo) implements DatabaseConnectionInterface {
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
                },
            );
        } catch (PDOException $e) {
            $this->markTestSkipped('Postgres недоступна: ' . $e->getMessage());
        }
    }

    public function testSaveNews(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $news = new News(
            title: 'Тестовая новость ' . uniqid('', true),
            content: 'Содержание тестовой новости',
            priority: 1,
            status: NewsStatus::Pending,
        );

        $saved = $this->repository->save($news);

        $this->assertNotNull($saved->getId());
        $this->assertEquals(NewsStatus::Pending, $saved->getStatus());
        $this->assertEquals(1, $saved->getPriority());
    }

    public function testFindById(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $news = new News(
            title: 'Новость для поиска ' . uniqid('', true),
            content: 'Содержание',
            status: NewsStatus::Pending,
        );

        $saved = $this->repository->save($news);
        $found = $this->repository->findById($saved->getId());

        $this->assertNotNull($found);
        $this->assertEquals($saved->getId(), $found->getId());
        $this->assertEquals($saved->getTitle(), $found->getTitle());
    }

    public function testFindByIdNotFound(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $found = $this->repository->findById(999999);
        $this->assertNull($found);
    }

    public function testFindPending(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $news = new News(
            title: 'Pending новость ' . uniqid('', true),
            content: 'Содержание',
            status: NewsStatus::Pending,
        );
        $this->repository->save($news);

        $pending = $this->repository->findPending();
        $this->assertIsArray($pending);
        $this->assertNotEmpty($pending);
    }

    public function testMarkAsDelivering(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $news = new News(
            title: 'Для доставки ' . uniqid('', true),
            content: 'Содержание',
            status: NewsStatus::Pending,
        );

        $saved = $this->repository->save($news);
        $this->repository->markAsDelivering($saved->getId());

        $found = $this->repository->findById($saved->getId());
        $this->assertEquals(NewsStatus::Delivering, $found->getStatus());
    }

    public function testMarkAsDelivered(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $news = new News(
            title: 'Для завершения ' . uniqid('', true),
            content: 'Содержание',
            status: NewsStatus::Delivering,
        );

        $saved = $this->repository->save($news);
        $this->repository->markAsDelivered($saved->getId());

        $found = $this->repository->findById($saved->getId());
        $this->assertEquals(NewsStatus::Delivered, $found->getStatus());
    }
}
