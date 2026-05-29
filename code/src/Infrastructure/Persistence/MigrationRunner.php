<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Persistence;

use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use PDO;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;

/**
 * Механизм выполнения SQL-миграций с защитой от параллельного запуска через pg_advisory_lock
 */
class MigrationRunner
{
    private const ADVISORY_LOCK_ID = 20260529; // Фиксированный ID для advisory lock

    public function __construct(
        private readonly DatabaseConnectionInterface $db,
        private readonly LoggerInterface $logger,
        private readonly string $migrationsDir,
    ) {
    }

    /**
     * Выполняет все неприменённые миграции из директории
     *
     * @return int Количество выполненных миграций
     */
    public function run(): int
    {
        $pdo = $this->db->getConnection();

        // Получаем advisory lock для защиты от параллельного запуска
        $lockStmt = $pdo->query('SELECT pg_try_advisory_lock(' . self::ADVISORY_LOCK_ID . ')');
        $locked = $lockStmt->fetchColumn();

        if (!$locked) {
            $this->logger->warning('Миграции не выполнены: pg_advisory_lock занят (возможно, другой слот выполняет миграции)');
            return 0;
        }

        try {
            // Создаём таблицу миграций (если не существует)
            $this->ensureMigrationsTable();

            $applied = $this->getAppliedMigrations();
            $available = $this->getAvailableMigrations();
            $pending = array_diff($available, $applied);

            if (empty($pending)) {
                $this->logger->info('Нет неприменённых миграций');
                return 0;
            }

            sort($pending);

            $count = 0;
            foreach ($pending as $migration) {
                $this->executeMigration($migration);
                $count++;
            }

            $this->logger->info("Выполнено миграций: {$count}");

            return $count;
        } finally {
            // Освобождаем advisory lock
            $pdo->query('SELECT pg_advisory_unlock(' . self::ADVISORY_LOCK_ID . ')');
        }
    }

    /**
     * Создаёт таблицу schema_migrations, если она не существует
     */
    private function ensureMigrationsTable(): void
    {
        $pdo = $this->db->getConnection();
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS schema_migrations (
                version VARCHAR(255) PRIMARY KEY,
                applied_at TIMESTAMP NOT NULL DEFAULT NOW()
            )
        ");
    }

    /**
     * Возвращает список уже применённых миграций
     *
     * @return array<string>
     */
    private function getAppliedMigrations(): array
    {
        $pdo = $this->db->getConnection();
        $stmt = $pdo->query('SELECT version FROM schema_migrations ORDER BY version');
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Возвращает список доступных миграций из директории
     *
     * @return array<string>
     */
    private function getAvailableMigrations(): array
    {
        $files = glob($this->migrationsDir . '/*.sql');
        if ($files === false) {
            return [];
        }

        return array_map(fn (string $f) => pathinfo($f, PATHINFO_FILENAME), $files);
    }

    /**
     * Выполняет одну миграцию в транзакции
     */
    private function executeMigration(string $version): void
    {
        $pdo = $this->db->getConnection();
        $filePath = $this->migrationsDir . '/' . $version . '.sql';

        if (!file_exists($filePath)) {
            throw new RuntimeException("Файл миграции не найден: {$filePath}");
        }

        $sql = file_get_contents($filePath);
        if ($sql === false) {
            throw new RuntimeException("Не удалось прочитать файл миграции: {$filePath}");
        }

        $pdo->beginTransaction();
        try {
            $pdo->exec($sql);
            $stmt = $pdo->prepare('INSERT INTO schema_migrations (version) VALUES (?)');
            $stmt->execute([$version]);
            $pdo->commit();
            $this->logger->info("Миграция выполнена: {$version}");
        } catch (Throwable $e) {
            $pdo->rollBack();
            $this->logger->error("Ошибка миграции {$version}: " . $e->getMessage());
            throw $e;
        }
    }
}
