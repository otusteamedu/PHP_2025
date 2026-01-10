<?php

declare(strict_types=1);

namespace App\Models;

use App\Infrastructure\Database\PostgresConnection;

abstract class BaseModel
{
    protected ?int $id;
    protected PostgresConnection $connection;
    protected static array $identityMap = [];

    public function __construct(?int $id = null, ?PostgresConnection $connection = null)
    {
        $this->id = $id;
        $this->connection = $connection ?? new PostgresConnection();
    }

    /**
     * Имя таблицы, с которой работает модель.
     * 
     * @return string
     */
    abstract public static function getTableName(): string;

    /**
     * Список колонок, выбираемых в запросах SELECT.
     * Используется в методах find/all.
     * 
     * @return string[]
     */
    abstract protected static function getSelectColumns(): array;

    /**
     * Получить из массива модель.
     * @param array $data Данные строки из БД
     * 
     * @return static
     */
    abstract public static function fromArray(array $data): static;

    /**
     * Набор полей для INSERT/UPDATE.
     * 
     * @return array
     */
    abstract protected function getAttributes(): array;

    /**
     * Найти модель по ID.
     * @param int $id Идентификатор модели
     * @param ?PostgresConnection $db Коннектор к БД (если null, будет создан новый)
     * 
     * @return static|null
     */
    public static function find(int $id, ?PostgresConnection $db = null): ?static
    {
        if ($id <= 0) {
            return null;
        }

        if (($cached = static::getFromIdentityMap($id)) !== null) {
            return $cached;
        }

        $connection = static::resolveDb($db);
        $row = $connection->findById(static::getTableName(), static::getSelectColumns(), $id);

        if ($row === null) {
            return null;
        }

        $model = static::fromArray($row);
        $model->connection = $connection;

        static::storeInIdentityMap($model);

        return $model;
    }

    /**
     * Получить все модели из таблицы.
     * @param ?PostgresConnection $db Коннектор к БД (если null, будет создан новый)
     * 
     * @return static[]
     */
    public static function all(?PostgresConnection $db = null): array
    {
        $connection = static::resolveDb($db);
        $rows = $connection->fetchAll(static::getTableName(), static::getSelectColumns());

        $models = [];

        foreach ($rows as $row) {
            $id = isset($row['id']) ? (int)$row['id'] : null;

            $cached = static::getFromIdentityMap($id);

            if ($id !== null && $cached !== null) {
                $models[] = $cached;
                continue;
            }

            $model = static::fromArray($row);
            $model->connection = $connection;

            if ($id !== null) {
                static::storeInIdentityMap($model);
            }

            $models[] = $model;
        }

        return $models;
    }

    /**
     * Сохранить модель в БД.
     * 
     * @return int
     */
    public function save(): int
    {
        $connection = $this->connection;
        $attributes = $this->getAttributes();

        if ($this->id === null) {
            $this->id = (int)$connection->insert(static::getTableName(), $attributes);
            static::storeInIdentityMap($this);
            return 1;
        }

        $cnt = $connection->update(
            static::getTableName(),
            $attributes,
            '"id" = :id',
            ['id' => $this->id]
        );

        if ($cnt > 0) {
            static::storeInIdentityMap($this);
        }

        return $cnt;
    }

    /**
     * Удалить модель из БД.
     * 
     * @return int
     */
    public function delete(): int
    {
        if ($this->id === null) {
            return 0;
        }

        $cnt = $this->connection->delete(
            static::getTableName(),
            '"id" = :id',
            ['id' => $this->id]
        );

        if ($cnt > 0) {
            static::removeFromIdentityMap($this->id);
        }

        return $cnt;
    }

    /**
     * Получить идентификатор модели.
     * 
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Коннектор к БД.
     * @param ?PostgresConnection $db Коннектор к БД
     * 
     * @return PostgresConnection
     */
    protected static function resolveDb(?PostgresConnection $db): PostgresConnection
    {
        return $db ?? new PostgresConnection();
    }

    /**
     * Получить модель из Identity Map по ID.
     * @param int $id Идентификатор модели
     * 
     * @return static|null
     */
    protected static function getFromIdentityMap(int $id): ?static
    {
        return static::$identityMap[static::class][$id] ?? null;
    }

    /**
     * Добавить модель в Identity Map.
     * @param static $model Модель
     */
    protected static function storeInIdentityMap($model): void
    {
        if ($model->id !== null) {
            static::$identityMap[static::class][$model->id] = $model;
        }
    }

    /**
     * Удалить модель из Identity Map по ID.
     * @param int $id Идентификатор модели
     */
    protected static function removeFromIdentityMap(int $id): void
    {
        unset(static::$identityMap[static::class][$id]);
    }
}
