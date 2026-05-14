<?php

namespace App\Mapper;

use App\IdentityMap\IdentityMap;
use PDO;

/**
 * Базовый DataMapper для классов, которые преобразуют строки БД в объекты.
 */
abstract class Mapper
{
    protected PDO $connection;

    protected IdentityMap $identityMap;

    public function __construct(PDO $connection, IdentityMap $identityMap)
    {
        $this->connection = $connection;
        $this->identityMap = $identityMap;
    }

    /**
     * Возвращает имя таблицы, с которой работает конкретный mapper.
     */
    abstract protected function getTableName(): string;

    /**
     * Преобразует строку из БД в объект предметной области.
     */
    abstract protected function mapRowToObject(array $row): object;
}
