<?php

declare(strict_types=1);

namespace MkdBot\Tests\Integration\Persistence;

use MkdBot\Domain\Entity\BotSubscriber;
use MkdBot\Domain\Interface\BotSubscriberRepositoryInterface;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Infrastructure\Persistence\PostgresBotSubscriberRepository;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;

/**
 * Интеграционный тест PostgresBotSubscriberRepository
 * Требует запущенную БД Postgres — пропускается если БД недоступна
 */
class PostgresBotSubscriberRepositoryTest extends TestCase
{
    private ?BotSubscriberRepositoryInterface $repository = null;
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
            $this->repository = new PostgresBotSubscriberRepository(
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

    public function testSaveAndFindByUserId(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $userId = (int)(microtime(true) * 1000); // Уникальный ID для теста

        $subscriber = new BotSubscriber(
            userId: $userId,
            userName: 'Тестовый пользователь',
        );

        $this->repository->save($subscriber);
        $found = $this->repository->findByUserId($userId);

        $this->assertNotNull($found);
        $this->assertEquals($userId, $found->getUserId());
        $this->assertEquals('Тестовый пользователь', $found->getUserName());
        $this->assertTrue($found->isActive());
    }

    public function testFindByUserIdNotFound(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $found = $this->repository->findByUserId(999999999);
        $this->assertNull($found);
    }

    public function testFindActive(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $active = $this->repository->findActive();
        $this->assertIsArray($active);
    }

    public function testMarkAsInactiveAndActive(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $userId = (int)(microtime(true) * 1000) + 1;

        $subscriber = new BotSubscriber(
            userId: $userId,
            userName: 'Для деактивации',
        );
        $this->repository->save($subscriber);

        // Деактивируем
        $this->repository->markAsInactive($userId);
        $found = $this->repository->findByUserId($userId);
        $this->assertFalse($found->isActive());

        // Активируем обратно
        $this->repository->markAsActive($userId);
        $found = $this->repository->findByUserId($userId);
        $this->assertTrue($found->isActive());
    }
}
