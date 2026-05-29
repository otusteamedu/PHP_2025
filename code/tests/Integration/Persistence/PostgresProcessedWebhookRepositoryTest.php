<?php

declare(strict_types=1);

namespace MkdBot\Tests\Integration\Persistence;

use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Domain\Interface\ProcessedWebhookRepositoryInterface;
use MkdBot\Infrastructure\Persistence\PostgresProcessedWebhookRepository;
use PDO;
use PDOException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Интеграционный тест PostgresProcessedWebhookRepository
 * Требует запущенную БД Postgres — пропускается если БД недоступна
 */
class PostgresProcessedWebhookRepositoryTest extends TestCase
{
    private ?ProcessedWebhookRepositoryInterface $repository = null;
    private ?PDO $pdo = null;

    /** Тестовый mid для очистки */
    private const TEST_MID = 'mid.abc123def456';

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
            $this->repository = new PostgresProcessedWebhookRepository(
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

            // Очистка тестовых данных перед каждым тестом
            $this->pdo->prepare("DELETE FROM processed_webhooks WHERE message_mid LIKE 'mid.test%'")
                ->execute();
            $this->pdo->prepare("DELETE FROM processed_webhooks WHERE message_mid = ?")
                ->execute([self::TEST_MID]);
        } catch (PDOException $e) {
            $this->markTestSkipped('Postgres недоступна: ' . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        if ($this->pdo !== null) {
            $this->pdo->prepare("DELETE FROM processed_webhooks WHERE message_mid LIKE 'mid.test%'")
                ->execute();
            $this->pdo->prepare("DELETE FROM processed_webhooks WHERE message_mid = ?")
                ->execute([self::TEST_MID]);
        }
    }

    #[Test]
    public function testTryAcquireNewMidReturnsTrue(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        // Новый mid — tryAcquire возвращает true (строка вставлена)
        $result = $this->repository->tryAcquire('mid.test_new_123', 'max');

        $this->assertTrue($result);
    }

    #[Test]
    public function testTryAcquireDuplicateMidReturnsFalse(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $mid = 'mid.test_duplicate';

        // Первый вызов — true (строка вставлена)
        $first = $this->repository->tryAcquire($mid, 'max');
        $this->assertTrue($first);

        // Повторный вызов с тем же mid — false (конфликт, webhook уже обработан)
        $second = $this->repository->tryAcquire($mid, 'max');
        $this->assertFalse($second);
    }

    #[Test]
    public function testTryAcquireSameMidDifferentMessengerType(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $mid = 'mid.test_multi_messenger';

        // tryAcquire для max — true
        $this->assertTrue($this->repository->tryAcquire($mid, 'max'));

        // tryAcquire для telegram с тем же mid — тоже true (другой messenger_type)
        $this->assertTrue($this->repository->tryAcquire($mid, 'telegram'));

        // Повторный для max — false
        $this->assertFalse($this->repository->tryAcquire($mid, 'max'));

        // Повторный для telegram — false
        $this->assertFalse($this->repository->tryAcquire($mid, 'telegram'));

        // В таблице должно быть две записи с одним mid, но разными messenger_type
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM processed_webhooks WHERE message_mid = ?",
        );
        $stmt->execute([$mid]);
        $count = (int)$stmt->fetchColumn();
        $this->assertEquals(2, $count, 'Один mid с разными messenger_type = разные записи');
    }

    #[Test]
    public function testTryAcquireOnlyOneRecordInTable(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $mid = 'mid.test_idempotent';

        // Первый вызов — вставка
        $this->repository->tryAcquire($mid, 'max');

        // Повторный вызов — ON CONFLICT DO NOTHING, строка не вставляется
        $this->repository->tryAcquire($mid, 'max');

        // В таблице должна быть только одна запись
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM processed_webhooks WHERE message_mid = ? AND messenger_type = ?",
        );
        $stmt->execute([$mid, 'max']);
        $count = (int)$stmt->fetchColumn();
        $this->assertEquals(1, $count, 'Должна быть только одна запись для (mid, messenger_type)');
    }

    #[Test]
    public function testTryAcquireMessageMidStringFormat(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        // message_mid — строка формата mid.[0-9a-f]+, не числовой ID
        // Префикс 'mid.test' обязателен для очистки в setUp/tearDown
        $mid = 'mid.test_a1b2c3d4e5f6';

        $result = $this->repository->tryAcquire($mid, 'max');

        $this->assertTrue($result);

        // Повторный вызов — false
        $this->assertFalse($this->repository->tryAcquire($mid, 'max'));
    }
}
