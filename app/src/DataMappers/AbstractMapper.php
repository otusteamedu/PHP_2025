<?php

declare(strict_types=1);

namespace App\DataMappers;

use PDO;
use PDOStatement;

/**
 * Class AbstractMapper
 * @package App\DataMappers
 */
abstract class AbstractMapper
{
    /**
     * @var PDO
     */
    protected PDO $pdo;
    /**
     * @var PDOStatement
     */
    protected PDOStatement $insertStatement;
    /**
     * @var PDOStatement
     */
    protected PDOStatement $updateStatement;
    /**
     * @var PDOStatement
     */
    protected PDOStatement $deleteStatement;
    /**
     * @var PDOStatement
     */
    protected PDOStatement $findByIdStatement;
    /**
     * @var PDOStatement
     */
    protected PDOStatement $findAllStatement;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->insertStatement = $this->prepareInsertStatement();
        $this->updateStatement = $this->prepareUpdateStatement();
        $this->deleteStatement = $this->prepareDeleteStatement();
        $this->findByIdStatement = $this->prepareFindByIdStatement();
        $this->findAllStatement = $this->prepareFindAllStatement();
    }

    /**
     * @return string
     */
    abstract protected function getInsertStatementQuery(): string;

    /**
     * @return string
     */
    abstract protected function getUpdateStatementQuery(): string;

    /**
     * @return string
     */
    abstract protected function getDeleteStatementQuery(): string;

    /**
     * @return string
     */
    abstract protected function getFindByIdStatementQuery(): string;

    /**
     * @return string
     */
    abstract protected function getFindAllStatementQuery(): string;

    /**
     * @return false|PDOStatement
     */
    protected function prepareInsertStatement(): false|PDOStatement
    {
        return $this->pdo->prepare($this->getInsertStatementQuery());
    }

    /**
     * @return false|PDOStatement
     */
    protected function prepareUpdateStatement(): false|PDOStatement
    {
        return $this->pdo->prepare($this->getUpdateStatementQuery());
    }

    /**
     * @return false|PDOStatement
     */
    protected function prepareDeleteStatement(): false|PDOStatement
    {
        return $this->pdo->prepare($this->getDeleteStatementQuery());
    }

    /**
     * @return false|PDOStatement
     */
    protected function prepareFindByIdStatement(): false|PDOStatement
    {
        return $this->pdo->prepare($this->getFindByIdStatementQuery());
    }

    /**
     * @return false|PDOStatement
     */
    protected function prepareFindAllStatement(): false|PDOStatement
    {
        return $this->pdo->prepare($this->getFindAllStatementQuery());
    }
}
