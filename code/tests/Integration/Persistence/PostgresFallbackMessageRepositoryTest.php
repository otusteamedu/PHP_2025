<?php

declare(strict_types=1);

namespace MkdBot\Tests\Integration\Persistence;

use DateTimeImmutable;
use MkdBot\Domain\Entity\FallbackMessage;
use MkdBot\Domain\Enum\QueueNameType;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Domain\Interface\FallbackMessageRepositoryInterface;
use MkdBot\Infrastructure\Persistence\PostgresFallbackMessageRepository;
use PDO;
use PDOException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Интеграционный тест PostgresFallbackMessageRepository
 * Требует запущенную БД Postgres — пропускается если БД недоступна
 */
class PostgresFallbackMessageRepositoryTest extends TestCase
{
    private ?FallbackMessageRepositoryInterface $repository = null;
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
            $this->repository = new PostgresFallbackMessageRepository(
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
            $this->pdo->prepare("DELETE FROM fallback_messages WHERE queue_name = ?")
                ->execute([QueueNameType::TelegramForward->value]);
        } catch (PDOException $e) {
            $this->markTestSkipped('Postgres недоступна: ' . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        if ($this->pdo !== null) {
            $this->pdo->prepare("DELETE FROM fallback_messages WHERE queue_name = ?")
                ->execute([QueueNameType::TelegramForward->value]);
        }
    }

    #[Test]
    public function testSaveFallbackMessage(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $message = new FallbackMessage(
            queueName: QueueNameType::TelegramForward,
            messageBody: ['text' => 'Тестовое сообщение из DLQ', 'user_id' => 12345],
            errorMessage: 'Consumer timed out after 30s',
            xDeathCount: 0,
        );

        $saved = $this->repository->save($message);

        // После сохранения должен быть назначен id
        $this->assertNotNull($saved->getId());
        $this->assertGreaterThan(0, $saved->getId());
        $this->assertEquals(QueueNameType::TelegramForward, $saved->getQueueName());
        $this->assertEquals('Consumer timed out after 30s', $saved->getErrorMessage());
        $this->assertEquals(0, $saved->getXDeathCount());
    }

    #[Test]
    public function testSaveWithXDeathCount(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $message = new FallbackMessage(
            queueName: QueueNameType::TelegramForward,
            messageBody: ['payload' => 'data'],
            errorMessage: 'Retry limit exceeded',
            xDeathCount: 3,
        );

        $saved = $this->repository->save($message);

        $this->assertNotNull($saved->getId());
        $this->assertEquals(3, $saved->getXDeathCount());

        // Проверяем, что x_death_count корректно сохранён в БД
        $stmt = $this->pdo->prepare(
            "SELECT x_death_count FROM fallback_messages WHERE id = ?",
        );
        $stmt->execute([$saved->getId()]);
        $dbCount = (int)$stmt->fetchColumn();
        $this->assertEquals(3, $dbCount, 'x_death_count должен быть сохранён как 3');
    }

    #[Test]
    public function testSaveMessageBodyJsonSaveAndRead(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $body = [
            'message_id' => 'mid.abc123',
            'user_id' => 54321,
            'text' => 'Привет из Max!',
            'nested' => ['key' => 'value'],
        ];

        $message = new FallbackMessage(
            queueName: QueueNameType::TelegramForward,
            messageBody: $body,
            errorMessage: 'Connection refused',
            xDeathCount: 1,
        );

        $saved = $this->repository->save($message);

        // Проверяем, что message_body корректно сохранён и читается
        $stmt = $this->pdo->prepare(
            "SELECT message_body FROM fallback_messages WHERE id = ?",
        );
        $stmt->execute([$saved->getId()]);
        $rawBody = $stmt->fetchColumn();
        $decodedBody = json_decode($rawBody, true);

        $this->assertEquals($body, $decodedBody, 'message_body JSON должен корректно сохраняться и читаться');
    }

    #[Test]
    public function testSaveCreatedAtCorrectSave(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $now = new DateTimeImmutable();
        $message = new FallbackMessage(
            queueName: QueueNameType::TelegramForward,
            messageBody: ['test' => true],
            errorMessage: 'Test error',
            xDeathCount: 0,
            createdAt: $now,
        );

        $saved = $this->repository->save($message);

        // Проверяем, что created_at сохранён корректно
        $stmt = $this->pdo->prepare(
            "SELECT created_at FROM fallback_messages WHERE id = ?",
        );
        $stmt->execute([$saved->getId()]);
        $dbCreatedAt = $stmt->fetchColumn();

        $this->assertNotEmpty($dbCreatedAt, 'created_at должен быть заполнен');
        // Допускаем разницу в 1 секунду из-за округления формата
        $expectedTimestamp = $now->getTimestamp();
        $actualTimestamp = (new DateTimeImmutable($dbCreatedAt))->getTimestamp();
        $this->assertEqualsWithDelta(
            $expectedTimestamp,
            $actualTimestamp,
            1,
            'created_at должен соответствовать переданному значению',
        );
    }

    #[Test]
    public function testSaveMultipleMessages(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        // Сохраняем несколько fallback-сообщений
        for ($i = 1; $i <= 3; $i++) {
            $message = new FallbackMessage(
                queueName: QueueNameType::TelegramForward,
                messageBody: ['index' => $i],
                errorMessage: "Error #{$i}",
                xDeathCount: $i,
            );
            $saved = $this->repository->save($message);
            $this->assertNotNull($saved->getId(), "Сообщение #{$i} должно получить id");
        }

        // Проверяем, что все 3 записи сохранены
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM fallback_messages WHERE queue_name = ?",
        );
        $stmt->execute([QueueNameType::TelegramForward->value]);
        $count = (int)$stmt->fetchColumn();
        $this->assertGreaterThanOrEqual(3, $count, 'Должно быть сохранено не менее 3 fallback-сообщений');
    }
}
