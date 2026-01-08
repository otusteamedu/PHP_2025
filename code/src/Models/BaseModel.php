<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\PostgresService;

abstract class BaseModel
{
    protected ?int $id;
    protected PostgresService $service;
    protected static array $identityMap = [];

    public function __construct(?int $id = null, ?PostgresService $service = null)
    {
        $this->id = $id;
        $this->service = $service ?? new PostgresService();
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
     * @param ?PostgresService $db Сервис БД (если null, будет создан новый)
     * 
     * @return static|null
     */
    public static function find(int $id, ?PostgresService $db = null): ?static
    {
        if ($id <= 0) {
            return null;
        }

        if (($cached = static::getFromIdentityMap($id)) !== null) {
            return $cached;
        }

        $service = static::resolveDb($db);
        $row = $service->findById(static::getTableName(), static::getSelectColumns(), $id);

        if ($row === null) {
            return null;
        }

        $model = static::fromArray($row);
        $model->service = $service;

        static::storeInIdentityMap($model);

        return $model;
    }

    /**
     * Получить все модели из таблицы.
     * @param ?PostgresService $db Сервис БД (если null, будет создан новый)
     * 
     * @return static[]
     */
    public static function all(?PostgresService $db = null): array
    {
        $service = static::resolveDb($db);
        $rows = $service->fetchAll(static::getTableName(), static::getSelectColumns());

        $models = [];

        foreach ($rows as $row) {
            $id = isset($row['id']) ? (int)$row['id'] : null;

            $cached = static::getFromIdentityMap($id);

            if ($id !== null && $cached !== null) {
                $models[] = $cached;
                continue;
            }

            $model = static::fromArray($row);
            $model->service = $service;

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
        $service = $this->service;
        $attributes = $this->getAttributes();

        if ($this->id === null) {
            $this->id = (int)$service->insert(static::getTableName(), $attributes);
            static::storeInIdentityMap($this);
            return 1;
        }

        $cnt = $service->update(
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

        $cnt = $this->service->delete(
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
     * Cервис БД.
     * @param ?PostgresService $db Сервис БД
     * 
     * @return PostgresService
     */
    protected static function resolveDb(?PostgresService $db): PostgresService
    {
        return $db ?? new PostgresService();
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
