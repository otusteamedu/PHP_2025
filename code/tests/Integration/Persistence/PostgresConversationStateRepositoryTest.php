<?php

declare(strict_types=1);

namespace MkdBot\Tests\Integration\Persistence;

use DateTimeImmutable;
use MkdBot\Domain\Entity\ConversationState;
use MkdBot\Domain\Enum\ConversationStep;
use MkdBot\Domain\Interface\ConversationStateRepositoryInterface;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Infrastructure\Persistence\PostgresConversationStateRepository;
use PDO;
use PDOException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Интеграционный тест PostgresConversationStateRepository
 * Требует запущенную БД Postgres — пропускается если БД недоступна
 */
class PostgresConversationStateRepositoryTest extends TestCase
{
    private ?ConversationStateRepositoryInterface $repository = null;
    private ?PDO $pdo = null;

    /** Уникальный user_id для тестов, чтобы не конфликтовать с реальными данными */
    private const TEST_USER_ID = 888001;

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
            $this->repository = new PostgresConversationStateRepository(
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
            $this->pdo->prepare("DELETE FROM conversation_states WHERE user_id = ?")
                ->execute([self::TEST_USER_ID]);
        } catch (PDOException $e) {
            $this->markTestSkipped('Postgres недоступна: ' . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        if ($this->pdo !== null) {
            $this->pdo->prepare("DELETE FROM conversation_states WHERE user_id = ?")
                ->execute([self::TEST_USER_ID]);
        }
    }

    #[Test]
    public function testSaveNewState(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $state = new ConversationState(
            userId: self::TEST_USER_ID,
            currentStep: ConversationStep::AwaitingSubject,
            data: [],
        );

        $saved = $this->repository->save($state);

        $this->assertEquals(self::TEST_USER_ID, $saved->getUserId());
        $this->assertEquals(ConversationStep::AwaitingSubject, $saved->getCurrentStep());
    }

    #[Test]
    public function testSaveUpdateExistingState(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        // Сначала сохраняем начальное состояние
        $state = new ConversationState(
            userId: self::TEST_USER_ID,
            currentStep: ConversationStep::AwaitingSubject,
            data: ['subject' => 'Тема обращения'],
        );
        $this->repository->save($state);

        // Обновляем состояние (upsert через ON CONFLICT)
        $updated = new ConversationState(
            userId: self::TEST_USER_ID,
            currentStep: ConversationStep::AwaitingDescription,
            data: ['subject' => 'Новая тема', 'content' => 'Описание'],
        );
        $result = $this->repository->save($updated);

        // Проверяем, что данные обновились
        $found = $this->repository->findByUserId(self::TEST_USER_ID);
        $this->assertNotNull($found);
        $this->assertEquals(ConversationStep::AwaitingDescription, $found->getCurrentStep());
        $this->assertEquals('Новая тема', $found->getData()['subject']);
        $this->assertEquals('Описание', $found->getData()['content']);
    }

    #[Test]
    public function testFindByUserIdReturnsState(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $state = new ConversationState(
            userId: self::TEST_USER_ID,
            currentStep: ConversationStep::Preview,
            data: ['key' => 'value'],
        );
        $this->repository->save($state);

        $found = $this->repository->findByUserId(self::TEST_USER_ID);

        $this->assertNotNull($found);
        $this->assertEquals(self::TEST_USER_ID, $found->getUserId());
        $this->assertEquals(ConversationStep::Preview, $found->getCurrentStep());
    }

    #[Test]
    public function testFindByUserIdReturnsNullWhenNotFound(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $found = $this->repository->findByUserId(999999999);

        $this->assertNull($found);
    }

    #[Test]
    public function testDeleteByUserIdDeletesState(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $state = new ConversationState(
            userId: self::TEST_USER_ID,
            currentStep: ConversationStep::MainMenu,
        );
        $this->repository->save($state);

        // Убеждаемся, что запись существует
        $this->assertNotNull($this->repository->findByUserId(self::TEST_USER_ID));

        // Удаляем
        $result = $this->repository->deleteByUserId(self::TEST_USER_ID);

        $this->assertTrue($result);
        $this->assertNull($this->repository->findByUserId(self::TEST_USER_ID));
    }

    #[Test]
    public function testDeleteByUserIdNonExistentWithoutError(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        // Удаление несуществующего пользователя не должно вызывать исключение
        $result = $this->repository->deleteByUserId(999999999);

        $this->assertFalse($result);
    }

    #[Test]
    public function testExpiresAtCorrectSaveAndReadTTL(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $now = new DateTimeImmutable();
        $expiresAt = $now->modify('+30 minutes');

        $state = new ConversationState(
            userId: self::TEST_USER_ID,
            currentStep: ConversationStep::AwaitingQuestion,
            data: [],
            updatedAt: $now,
            expiresAt: $expiresAt,
        );
        $this->repository->save($state);

        $found = $this->repository->findByUserId(self::TEST_USER_ID);

        $this->assertNotNull($found);
        // Проверяем с точностью до секунды (БД хранит без микросекунд)
        $expectedTimestamp = $expiresAt->getTimestamp();
        $actualTimestamp = $found->getExpiresAt()->getTimestamp();
        // Допускаем разницу в 1 секунду из-за округления при сохранении
        $this->assertEqualsWithDelta(
            $expectedTimestamp,
            $actualTimestamp,
            1,
            'expires_at должен быть ~30 минут от текущего времени',
        );
    }

    #[Test]
    public function testDataJsonSaveAndRead(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $data = [
            'subject' => 'Проблема с отоплением',
            'content' => 'В квартире холодно, батареи не греют',
            'user_name' => 'Тестовый Пользователь',
            'type' => 'feature',
        ];

        $state = new ConversationState(
            userId: self::TEST_USER_ID,
            currentStep: ConversationStep::Preview,
            data: $data,
        );
        $this->repository->save($state);

        $found = $this->repository->findByUserId(self::TEST_USER_ID);

        $this->assertNotNull($found);
        $savedData = $found->getData();
        $this->assertEquals('Проблема с отоплением', $savedData['subject']);
        $this->assertEquals('В квартире холодно, батареи не греют', $savedData['content']);
        $this->assertEquals('Тестовый Пользователь', $savedData['user_name']);
        $this->assertEquals('feature', $savedData['type']);
    }
}
