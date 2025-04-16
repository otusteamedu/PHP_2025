<?php

namespace App\Mappers;

use App\Entities\Entity;
use App\Service\DB;
use Exception;

abstract class Mapper
{
    protected DB $db;

    protected string $table;

    /**
     * @template T of Entity
     * @var array<int,T>
     */
    private static array $identityMap = [];

    protected static function getRecord(int $id): ?Entity {
        return static::$identityMap[$id] ?? null;
    }

    /**
     * @throws Exception
     */
    protected static function addRecord(Entity $entity): void {
        if (empty($entity->getId())) {
            throw new Exception("id должно быть заполненным");
        }

        $id = $entity->getId();

        $record = static::getRecord($id);

        if (empty($record)) {
            static::$identityMap[$id] = $entity;
        }
    }

    /**
     * @throws Exception
     */
    protected static function deleteRecord(int $id): void {
        $record = static::getRecord($id);

        if (empty($record) === false) {
            unset(static::$identityMap[$id]);
        }
    }

    /**
     * @throws Exception
     */
    protected static function updateRecord(Entity $entity): void {
        $id = $entity->getId();
        $record = static::getRecord($id);

        if (empty($record) === false) {
            static::$identityMap[$id] = $entity;
        }
    }
}