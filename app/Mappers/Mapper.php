<?php

namespace App\Mappers;

use App\Entities\Entity;
use App\Service\DB;

class Mapper
{
    /** @var DB */
    protected DB $db;

    /** @var string */
    protected string $table;

    /** @var array */
    private static array $identityMap = [];

    /**
     * @param string $className
     * @param $id
     * @return mixed|null
     */
    protected static function getRecord(string $className, $id) {
        $record = static::$identityMap[self::getRecordKey($className, $id)] ?? null;

        if (empty($record) === false) {
            $record = new $className($record);
        }

        return $record;
    }

    /**
     * @param Entity $entity
     * @param $id
     * @return void
     */
    protected static function addRecord(Entity $entity, $id): void {
        $className = get_class($entity);
        $record = static::getRecord($className, $id);

        if (empty($record)) {
            static::$identityMap[self::getRecordKey($className, $id)] = $entity->toArray();
        }
    }

    /**
     * @param string $className
     * @param $id
     * @return string
     */
    private static function getRecordKey(string $className, $id): string {
        return "$className.$id";
    }
}