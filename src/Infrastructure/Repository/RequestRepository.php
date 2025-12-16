<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Infrastructure\DataBase\DataBaseConnection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

final class RequestRepository
{
    private QueryBuilder $qb;
    private Connection $connection;

    public function __construct(DataBaseConnection $dataBaseConnection)
    {
        $this->connection = $dataBaseConnection->getConnection();
        $this->qb = $this->connection->createQueryBuilder();
    }

    public function insert(): int
    {
        $id = 0;
        $this->qb->insert('request')
            ->values(['status' => '?'])
            ->setParameter(0, false, ParameterType::BOOLEAN);

        try {
            $rows = $this->qb->executeStatement();

            if ($rows === 1) {
                $id = \intval($this->connection->lastInsertId());
            }
        } catch (Exception $e) {
        }

        return $id;
    }

    public function find(string|int $id): bool|array
    {
        $this->qb->select('status')
            ->from('request')
            ->where('id = :id')
            ->setParameter('id', $id);

        try {
            $result = $this->qb->executeQuery();

            return $result->fetchAssociative();
        } catch (Exception $e) {
            return false;
        }
    }

    public function update(string|int $id, bool $status = false): bool
    {
        $this->qb->update('request')
            ->set('status', ':status')
            ->where('id = :id')
            ->setParameter('status', $status, ParameterType::BOOLEAN)
            ->setParameter('id', $id)
        ;

        try {
            $rows = $this->qb->executeStatement();

            return $rows === 1;
        } catch (Exception $e) {
            return false;
        }
    }
}
