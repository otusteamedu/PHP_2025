<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Infrastructure\Persistence;

use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Infrastructure\Persistence\MigrationRunner;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Юнит-тесты для MigrationRunner
 *
 * Мокаем DatabaseConnectionInterface (PDO) и LoggerInterface
 * для тестирования логики выполнения миграций без реальной БД.
 */
class MigrationRunnerTest extends TestCase
{
    private DatabaseConnectionInterface $db;
    private LoggerInterface $logger;
    private string $migrationsDir;

    protected function setUp(): void
    {
        // Мокаем DatabaseConnectionInterface
        $this->db = $this->createMock(DatabaseConnectionInterface::class);

        // Мокаем логгер
        $this->logger = $this->createMock(LoggerInterface::class);

        // Создаём временную директорию для миграций
        $this->migrationsDir = sys_get_temp_dir() . '/migrations_test_' . uniqid(more_entropy: true);
        mkdir($this->migrationsDir, 0777, true);
    }

    protected function tearDown(): void
    {
        // Удаляем временную директорию
        $files = glob($this->migrationsDir . '/*.sql');
        if ($files !== false) {
            foreach ($files as $file) {
                unlink($file);
            }
        }
        if (is_dir($this->migrationsDir)) {
            rmdir($this->migrationsDir);
        }
    }

    /**
     * Создаёт файл миграции во временной директории
     */
    private function createMigrationFile(string $version, string $sql = 'SELECT 1'): void
    {
        file_put_contents($this->migrationsDir . '/' . $version . '.sql', $sql);
    }

    /**
     * Создаёт мок PDO с настроенным advisory lock
     */
    private function createPdoWithLock(bool $locked = true): PDO
    {
        $lockStmt = $this->createMock(PDOStatement::class);
        $lockStmt->method('fetchColumn')->willReturn($locked);

        $unlockStmt = $this->createMock(PDOStatement::class);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willReturnCallback(function (string $sql) use ($lockStmt, $unlockStmt) {
            if (str_contains($sql, 'pg_try_advisory_lock')) {
                return $lockStmt;
            }
            if (str_contains($sql, 'pg_advisory_unlock')) {
                return $unlockStmt;
            }
            if (str_contains($sql, 'SELECT version FROM schema_migrations')) {
                $stmt = $this->createMock(PDOStatement::class);
                $stmt->method('fetchAll')->with(PDO::FETCH_COLUMN)->willReturn([]);
                return $stmt;
            }
            return $this->createMock(PDOStatement::class);
        });

        // PDO::exec() возвращает false|int
        $pdo->method('exec')->willReturn(0);

        $this->db->method('getConnection')->willReturn($pdo);

        return $pdo;
    }

    /**
     * run() — возвращает 0 если не удалось получить advisory lock
     */
    public function testRunReturnsZeroWhenAdvisoryLockFailed(): void
    {
        $lockStmt = $this->createMock(PDOStatement::class);
        $lockStmt->method('fetchColumn')->willReturn(false);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willReturnCallback(function (string $sql) use ($lockStmt) {
            if (str_contains($sql, 'pg_try_advisory_lock')) {
                return $lockStmt;
            }
            return $this->createMock(PDOStatement::class);
        });

        $this->db->method('getConnection')->willReturn($pdo);
        $this->logger->expects($this->once())->method('warning')->with($this->stringContains('pg_advisory_lock'));

        $runner = new MigrationRunner($this->db, $this->logger, $this->migrationsDir);
        $result = $runner->run();

        $this->assertSame(0, $result);
    }

    /**
     * run() — возвращает 0 когда нет неприменённых миграций
     */
    public function testRunReturnsZeroWhenNoPendingMigrations(): void
    {
        $this->createPdoWithLock(true);

        // Нет файлов миграций — лог "Нет неприменённых миграций"
        $this->logger->expects($this->once())->method('info')->with($this->stringContains('Нет неприменённых'));

        $runner = new MigrationRunner($this->db, $this->logger, $this->migrationsDir);
        $result = $runner->run();

        $this->assertSame(0, $result);
    }

    /**
     * run() — выполняет одну миграцию и возвращает 1
     */
    public function testRunExecutesOnePendingMigration(): void
    {
        $this->createMigrationFile('001_create_users', 'CREATE TABLE users (id SERIAL)');

        $pdo = $this->createPdoWithLock(true);

        // Мокаем prepare для INSERT INTO schema_migrations
        $insertStmt = $this->createMock(PDOStatement::class);
        $insertStmt->method('execute')->willReturn(true);
        $pdo->method('prepare')->willReturn($insertStmt);

        // Мокаем транзакции
        $pdo->method('beginTransaction')->willReturn(true);
        $pdo->method('commit')->willReturn(true);

        $runner = new MigrationRunner($this->db, $this->logger, $this->migrationsDir);
        $result = $runner->run();

        $this->assertSame(1, $result);
    }

    /**
     * run() — выполняет несколько миграций по порядку
     */
    public function testRunExecutesMultiplePendingMigrationsInOrder(): void
    {
        $this->createMigrationFile('001_create_users', 'CREATE TABLE users (id SERIAL)');
        $this->createMigrationFile('002_create_posts', 'CREATE TABLE posts (id SERIAL)');
        $this->createMigrationFile('003_add_index', 'CREATE INDEX idx_posts ON posts(id)');

        $pdo = $this->createPdoWithLock(true);

        $insertStmt = $this->createMock(PDOStatement::class);
        $insertStmt->method('execute')->willReturn(true);
        $pdo->method('prepare')->willReturn($insertStmt);
        $pdo->method('beginTransaction')->willReturn(true);
        $pdo->method('commit')->willReturn(true);

        $runner = new MigrationRunner($this->db, $this->logger, $this->migrationsDir);
        $result = $runner->run();

        $this->assertSame(3, $result);
    }

    /**
     * run() — пропускает уже применённые миграции
     */
    public function testRunSkipsAlreadyAppliedMigrations(): void
    {
        $this->createMigrationFile('001_create_users', 'CREATE TABLE users (id SERIAL)');
        $this->createMigrationFile('002_create_posts', 'CREATE TABLE posts (id SERIAL)');

        $lockStmt = $this->createMock(PDOStatement::class);
        $lockStmt->method('fetchColumn')->willReturn(true);
        $unlockStmt = $this->createMock(PDOStatement::class);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willReturnCallback(function (string $sql) use ($lockStmt, $unlockStmt) {
            if (str_contains($sql, 'pg_try_advisory_lock')) {
                return $lockStmt;
            }
            if (str_contains($sql, 'pg_advisory_unlock')) {
                return $unlockStmt;
            }
            if (str_contains($sql, 'SELECT version FROM schema_migrations')) {
                // 001 уже применена
                $stmt = $this->createMock(PDOStatement::class);
                $stmt->method('fetchAll')->with(PDO::FETCH_COLUMN)->willReturn(['001_create_users']);
                return $stmt;
            }
            return $this->createMock(PDOStatement::class);
        });

        $pdo->method('exec')->willReturn(0);

        $insertStmt = $this->createMock(PDOStatement::class);
        $insertStmt->method('execute')->willReturn(true);
        $pdo->method('prepare')->willReturn($insertStmt);
        $pdo->method('beginTransaction')->willReturn(true);
        $pdo->method('commit')->willReturn(true);

        $this->db->method('getConnection')->willReturn($pdo);

        $runner = new MigrationRunner($this->db, $this->logger, $this->migrationsDir);
        $result = $runner->run();

        // Должна выполниться только 002
        $this->assertSame(1, $result);
    }

    /**
     * run() — при ошибке миграции вызывает rollback и логирует ошибку
     */
    public function testRunRollsBackOnMigrationError(): void
    {
        $this->createMigrationFile('001_create_users', 'INVALID SQL');

        $lockStmt = $this->createMock(PDOStatement::class);
        $lockStmt->method('fetchColumn')->willReturn(true);
        $unlockStmt = $this->createMock(PDOStatement::class);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willReturnCallback(function (string $sql) use ($lockStmt, $unlockStmt) {
            if (str_contains($sql, 'pg_try_advisory_lock')) {
                return $lockStmt;
            }
            if (str_contains($sql, 'pg_advisory_unlock')) {
                return $unlockStmt;
            }
            if (str_contains($sql, 'SELECT version FROM schema_migrations')) {
                $stmt = $this->createMock(PDOStatement::class);
                $stmt->method('fetchAll')->with(PDO::FETCH_COLUMN)->willReturn([]);
                return $stmt;
            }
            return $this->createMock(PDOStatement::class);
        });

        // exec вызывается дважды: ensureMigrationsTable (успех) и executeMigration (ошибка)
        $callCount = 0;
        $pdo->method('exec')->willReturnCallback(function () use (&$callCount) {
            $callCount++;
            if ($callCount === 1) {
                // ensureMigrationsTable — успех
                return 0;
            }
            // executeMigration — ошибка
            throw new RuntimeException('SQL syntax error');
        });

        $pdo->method('beginTransaction')->willReturn(true);
        $pdo->method('rollBack')->willReturn(true);

        $this->db->method('getConnection')->willReturn($pdo);

        $this->logger->expects($this->once())->method('error')->with($this->stringContains('Ошибка миграции'));

        $runner = new MigrationRunner($this->db, $this->logger, $this->migrationsDir);

        $this->expectException(RuntimeException::class);
        $runner->run();
    }

    /**
     * run() — освобождает advisory lock даже при исключении (finally)
     */
    public function testRunReleasesAdvisoryLockEvenOnException(): void
    {
        $this->createMigrationFile('001_broken', 'BROKEN SQL');

        $lockStmt = $this->createMock(PDOStatement::class);
        $lockStmt->method('fetchColumn')->willReturn(true);

        $unlockCalled = false;
        $unlockStmt = $this->createMock(PDOStatement::class);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willReturnCallback(function (string $sql) use ($lockStmt, $unlockStmt, &$unlockCalled) {
            if (str_contains($sql, 'pg_try_advisory_lock')) {
                return $lockStmt;
            }
            if (str_contains($sql, 'pg_advisory_unlock')) {
                $unlockCalled = true;
                return $unlockStmt;
            }
            if (str_contains($sql, 'SELECT version FROM schema_migrations')) {
                $stmt = $this->createMock(PDOStatement::class);
                $stmt->method('fetchAll')->with(PDO::FETCH_COLUMN)->willReturn([]);
                return $stmt;
            }
            return $this->createMock(PDOStatement::class);
        });

        $callCount = 0;
        $pdo->method('exec')->willReturnCallback(function () use (&$callCount) {
            $callCount++;
            if ($callCount === 1) {
                return 0; // ensureMigrationsTable
            }
            throw new RuntimeException('fail');
        });

        $pdo->method('beginTransaction')->willReturn(true);
        $pdo->method('rollBack')->willReturn(true);

        $this->db->method('getConnection')->willReturn($pdo);

        $runner = new MigrationRunner($this->db, $this->logger, $this->migrationsDir);

        try {
            $runner->run();
        } catch (RuntimeException) {
            // Ожидаемое исключение
        }

        $this->assertTrue($unlockCalled, 'advisory unlock должен быть вызван в finally');
    }

    /**
     * run() — вызывает getConnection() для получения PDO
     */
    public function testRunCallsGetConnection(): void
    {
        $lockStmt = $this->createMock(PDOStatement::class);
        $lockStmt->method('fetchColumn')->willReturn(false); // lock не получен

        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willReturnCallback(function (string $sql) use ($lockStmt) {
            if (str_contains($sql, 'pg_try_advisory_lock')) {
                return $lockStmt;
            }
            return $this->createMock(PDOStatement::class);
        });

        $this->db->expects($this->once())->method('getConnection')->willReturn($pdo);

        $runner = new MigrationRunner($this->db, $this->logger, $this->migrationsDir);
        $runner->run();
    }

    /**
     * run() — логирует информацию о выполненных миграциях
     */
    public function testRunLogsInfoOnSuccessfulMigration(): void
    {
        $this->createMigrationFile('001_create_users', 'CREATE TABLE users (id SERIAL)');

        $pdo = $this->createPdoWithLock(true);

        $insertStmt = $this->createMock(PDOStatement::class);
        $insertStmt->method('execute')->willReturn(true);
        $pdo->method('prepare')->willReturn($insertStmt);
        $pdo->method('beginTransaction')->willReturn(true);
        $pdo->method('commit')->willReturn(true);

        // Ожидаем логирование: "Миграция выполнена: 001_create_users" и "Выполнено миграций: 1"
        $this->logger->expects($this->exactly(2))->method('info');

        $runner = new MigrationRunner($this->db, $this->logger, $this->migrationsDir);
        $result = $runner->run();

        $this->assertSame(1, $result);
    }
}
