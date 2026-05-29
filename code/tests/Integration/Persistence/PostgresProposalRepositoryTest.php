<?php

declare(strict_types=1);

namespace MkdBot\Tests\Integration\Persistence;

use MkdBot\Domain\Entity\Proposal;
use MkdBot\Domain\Enum\ProposalType;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Domain\Interface\ProposalRepositoryInterface;
use MkdBot\Infrastructure\Persistence\PostgresProposalRepository;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;

/**
 * Интеграционный тест PostgresProposalRepository
 * Требует запущенную БД Postgres — пропускается если БД недоступна
 */
class PostgresProposalRepositoryTest extends TestCase
{
    private ?ProposalRepositoryInterface $repository = null;
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
            $this->repository = new PostgresProposalRepository(
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

    public function testSaveProposal(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $proposal = new Proposal(
            type: ProposalType::Feature,
            userId: 99999,
            userName: 'Test User',
            subject: 'Test subject ' . uniqid('', true),
            content: 'Test content for integration test',
        );

        $saved = $this->repository->save($proposal);

        $this->assertNotNull($saved->getId());
        $this->assertEquals(ProposalType::Feature, $saved->getType());
        $this->assertEquals(99999, $saved->getUserId());
        $this->assertEquals('Test User', $saved->getUserName());
    }

    public function testSaveSuggestionProposal(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $proposal = new Proposal(
            type: ProposalType::Suggestion,
            userId: 99998,
            userName: 'Suggester',
            subject: 'Suggestion ' . uniqid('', true),
            content: 'Suggestion content',
        );

        $saved = $this->repository->save($proposal);

        $this->assertNotNull($saved->getId());
        $this->assertEquals(ProposalType::Suggestion, $saved->getType());
    }
}
