<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Infrastructure\Repository;

use Dinargab\Homework20\Domain\Statement\Entity\BankStatement;
use Dinargab\Homework20\Domain\Statement\Factory\BankStatementFactoryInterface;
use Dinargab\Homework20\Domain\Statement\Repository\BankStatementRepositoryInterface;
use Dinargab\Homework20\Infrastructure\Client\PostgreSQLClient;
use ReflectionClass;

class StatementRepository implements BankStatementRepositoryInterface
{
    public function __construct(
        private readonly PostgreSQLClient $client,
        private readonly BankStatementFactoryInterface $statementFactory
    )
    {

    }

    public function addStatement(BankStatement $statement): BankStatement
    {
        $stmt = $this->client->getConnection()->prepare("INSERT INTO bankstatement (date_from, date_to, url) VALUES (:date_from, :date_to, :url) RETURNING id");
        $stmt->bindValue(':date_from', (string) $statement->getDateFrom());
        $stmt->bindValue(':date_to', (string) $statement->getDateTo());
        $stmt->bindValue(':url', $statement->getUrl());
        $stmt->execute();
        $lastId = $stmt->fetchColumn();
        $reflection = new ReflectionClass($statement);
        $reflectionProperty = $reflection->getProperty("id");
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($statement, $lastId);
        $reflectionProperty->setAccessible(false);
        return $statement;
    }

    public function getAllStatements(): array
    {
        $query = $this->client->getConnection()->query("SELECT * FROM bankstatement");
        $statements = [];
        foreach ($query as $statement) {
            $statements[] = $this->statementFactory->create($statement["date_from"], $statement["date_to"], $statement["url"]);
        }
        return $statements;
    }

    public function getStatementById(int $id): BankStatement
    {
        $query = $this->client->getConnection()->prepare("SELECT * FROM bankstatement WHERE id = :id");
        $query->bindValue(":id", $id);
        $query->execute();
        $result = $query->fetch();
        $statement = $this->statementFactory->create($result["date_from"], $result["date_to"], $result["url"]);
        $reflection = new ReflectionClass($statement);
        $reflectionProperty = $reflection->getProperty("id");
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($statement, $result['id']);
        $reflectionProperty->setAccessible(false);

        return $statement;
    }
}