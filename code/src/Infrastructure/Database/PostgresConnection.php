<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use InvalidArgumentException;
use PDO;
use PDOException;
use Generator;

class PostgresConnection
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        if ($pdo instanceof PDO) {
            $this->pdo = $pdo;
            return;
        }

        if (!in_array('pgsql', PDO::getAvailableDrivers(), true)) {
            throw new PDOException('PDO pgsql driver is not installed or enabled');
        }

        $host = getenv('POSTGRES_HOST') ?: 'postgres';
        $port = (int)(getenv('POSTGRES_PORT') ?: 5432);
        $db = getenv('POSTGRES_DB') ?: 'app';
        $user = getenv('POSTGRES_USER') ?: 'app';
        $password = getenv('POSTGRES_PASSWORD') ?: 'app';

        $dsn = sprintf('pgsql:host=%s;port=%d;dbname=%s', $host, $port, $db);

        $this->pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    /**
     * Вставляет новую строку и возвращает вставленный ID.
     * @param string $table Название таблицы
     * @param array $data Пары колонка-значение
     * 
     * @return string Вставленный ID
     */
    public function insert(string $table, array $data): string
    {
        if ($data === []) {
            throw new InvalidArgumentException('Insert data must not be empty');
        }

        $columns = array_keys($data);
        $placeholders = array_map(static fn(string $col): string => ':' . $col, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->quoteIdentifier($table),
            implode(', ', array_map([$this, 'quoteIdentifier'], $columns)),
            implode(', ', $placeholders)
        );

        $stmt = $this->pdo->prepare($sql);
        foreach ($data as $col => $value) {
            $stmt->bindValue(':' . $col, $value);
        }
        $stmt->execute();

        return $this->pdo->lastInsertId();
    }

    /**
     * Обновляет строки, соответствующие условию WHERE, и возвращает количество затронутых строк.
     * @param string $table Название таблицы
     * @param array $data Пары колонка-значение для обновления
     * @param string $where Условие WHERE (без ключевого слова WHERE)
     * @param array $params Параметры для привязки в условии WHERE
     * 
     * @return int Количество затронутых строк
     */
    public function update(string $table, array $data, string $where, array $params = []): int
    {
        if ($data === []) {
            throw new InvalidArgumentException('Update data must not be empty');
        }

        $assignments = [];
        foreach (array_keys($data) as $col) {
            $assignments[] = sprintf('%s = :set_%s', $this->quoteIdentifier($col), $col);
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $this->quoteIdentifier($table),
            implode(', ', $assignments),
            $where
        );

        $stmt = $this->pdo->prepare($sql);
        foreach ($data as $col => $value) {
            $stmt->bindValue(':set_' . $col, $value);
        }
        $this->bindParams($stmt, $params);

        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Удаляет строки, соответствующие условию WHERE, и возвращает количество затронутых строк.
     * @param string $table Название таблицы
     * @param string $where Условие WHERE (без ключевого слова WHERE)
     * @param array $params Параметры для привязки в условии WHERE
     * 
     * @return int Количество затронутых строк
     */
    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = sprintf('DELETE FROM %s WHERE %s', $this->quoteIdentifier($table), $where);

        $stmt = $this->pdo->prepare($sql);
        $this->bindParams($stmt, $params);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Возвращает одну строку по id или null, если не найдено.
     * @param string $table Таблица
     * @param string[] $columns Колонки для выборки
     * 
     * @return array|null
     */
    public function findById(string $table, array $columns, int $id): ?array
    {
        if ($columns === []) {
            throw new InvalidArgumentException('Select columns must not be empty');
        }

        $columnList = implode(', ', array_map([$this, 'quoteIdentifier'], $columns));

        $stmt = $this->pdo->prepare(
            sprintf(
                'SELECT %s FROM %s WHERE %s = :id LIMIT 1',
                $columnList,
                $this->quoteIdentifier($table),
                $this->quoteIdentifier('id')
            )
        );

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : $row;
    }

    /**
     * Возвращает все строки, отсортированные по id, по частям (батчами).
     * @param string $table Таблица
     * @param string[] $columns Колонки для выборки
     * @param int $batchSize Размер батча
     * @param int $cursorId Начать после id
     * 
     * @return Generator<array>
     */
    public function fetchChunked(string $table, array $columns, int $batchSize = 1000, int $cursorId = 0): Generator
    {
        if ($columns === []) {
            throw new InvalidArgumentException('Select columns must not be empty');
        }

        if ($batchSize <= 0) {
            throw new InvalidArgumentException('Batch size must be positive');
        }

        $columnList = implode(', ', array_map([$this, 'quoteIdentifier'], $columns));
        $orderColumn = $this->quoteIdentifier('id');

        while (true) {
            $stmt = $this->pdo->prepare(
                sprintf(
                    'SELECT %s FROM %s WHERE %s > :after ORDER BY %s LIMIT :limit',
                    $columnList,
                    $this->quoteIdentifier($table),
                    $orderColumn,
                    $orderColumn
                )
            );

            $stmt->bindValue(':after', $cursorId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $batchSize, PDO::PARAM_INT);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($rows === []) {
                break;
            }

            foreach ($rows as $row) {
                yield $row;

                if (isset($row['id'])) {
                    $cursorId = (int) $row['id'];
                }
            }
        }
    }

    /**
     * Создает таблицу с указанными колонками.
     * @param string $name Название таблицы
     * @param array $columns Колонки таблицы в формате "имя => определение"
     * @param bool $ifNotExists Добавлять ли IF NOT EXISTS
     * 
     * @return void
     */
    public function createTable(string $name, array $columns = [], bool $ifNotExists = true): void
    {
        if ($columns === []) {
            throw new InvalidArgumentException('Table columns must not be empty');
        }

        foreach ($columns as $colName => $colDef) {
            $columns[$colName] = sprintf('%s %s', $this->quoteIdentifier((string)$colName), $colDef);
        }

        $ifClause = $ifNotExists ? ' IF NOT EXISTS' : '';
        $sql = sprintf('CREATE TABLE%s %s (%s)', $ifClause, $this->quoteIdentifier($name), implode(', ', $columns));
        $this->pdo->exec($sql);
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '"' . str_replace('"', '""', $identifier) . '"';
    }

    private function bindParams(\PDOStatement $stmt, array $params): void
    {
        foreach ($params as $key => $value) {
            if (is_int($key)) {
                $stmt->bindValue($key + 1, $value);
                continue;
            }

            $name = ':' . ltrim((string)$key, ':');
            $stmt->bindValue($name, $value);
        }
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}
