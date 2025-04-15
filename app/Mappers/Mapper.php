<?php

namespace App\Mappers;

use App\Entities\Entity;
use App\Service\DB;
use Exception;

class Mapper
{
    protected DB $db;

    protected string $table;

    private static array $identityMap = [];

    protected static function getRecord(string $className, int $id): ?Entity {
        return static::$identityMap[self::getRecordKey($className, $id)] ?? null;
    }

    /**
     * @throws Exception
     */
    protected static function addRecord(Entity $entity): void {
        if (empty($entity->getId())) {
            throw new Exception("id должно быть заполненым");
        }

        $className = get_class($entity);
        $id = $entity->getId();

        $record = static::getRecord($className, $id);

        if (empty($record)) {
            static::$identityMap[self::getRecordKey($className, $id)] = $entity;
        }
    }

    /**
     * @throws Exception
     */
    protected static function deleteRecord(string $className, int $id): void {
        $record = static::getRecord($className, $id);

        if (empty($record) === false) {
            unset(static::$identityMap[self::getRecordKey($className, $id)]);
        }
    }

    /**
     * @throws Exception
     */
    protected static function updateRecord(Entity $entity): void {
        $className = get_class($entity);
        $id = $entity->getId();
        $record = static::getRecord($className, $id);

        if (empty($record) === false) {
            static::$identityMap[self::getRecordKey($className, $id)] = $entity;
        }
    }

    private static function getRecordKey(string $className, $id): string {
        return "$className.$id";
    }

    public function getDB(): DB {
        return $this->db;
    }
}