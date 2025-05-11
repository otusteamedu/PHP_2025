<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\DataMappers;

use PDO;
use PDOStatement;

abstract class AbstractMapper
{

    protected PDO $pdo;
    protected PDOStatement $insertStatement;
    protected PDOStatement $updateStatement;
    protected PDOStatement $deleteStatement;
    protected PDOStatement $findByIdStatement;
    protected PDOStatement $findAllStatement;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->insertStatement = $this->prepareInsertStatement();
        $this->updateStatement = $this->prepareUpdateStatement();
        $this->deleteStatement = $this->prepareDeleteStatement();
        $this->findByIdStatement = $this->prepareFindByIdStatement();
        $this->findAllStatement = $this->prepareFindAllStatement();
    }

    abstract protected function getInsertStatementQuery(): string;

    abstract protected function getUpdateStatementQuery(): string;

    abstract protected function getDeleteStatementQuery(): string;

    abstract protected function getFindByIdStatementQuery(): string;

    abstract protected function getFindAllStatementQuery(): string;

    protected function prepareInsertStatement(): false|PDOStatement
    {
        return $this->pdo->prepare($this->getInsertStatementQuery());
    }

    protected function prepareUpdateStatement(): false|PDOStatement
    {
        return $this->pdo->prepare($this->getUpdateStatementQuery());
    }

    protected function prepareDeleteStatement(): false|PDOStatement
    {
        return $this->pdo->prepare($this->getDeleteStatementQuery());
    }

    protected function prepareFindByIdStatement(): false|PDOStatement
    {
        return $this->pdo->prepare($this->getFindByIdStatementQuery());
    }

    protected function prepareFindAllStatement(): false|PDOStatement
    {
        return $this->pdo->prepare($this->getFindAllStatementQuery());
    }
}
