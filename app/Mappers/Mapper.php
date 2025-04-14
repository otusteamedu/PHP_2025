<?php

namespace App\Mappers;

use App\Service\DB;
use Exception;

class Mapper
{
    protected DB $db;

    protected string $table;

    private static array $identityMap = [];

    protected static function getRecord(string $className, $id): ?array {
        return static::$identityMap[self::getRecordKey($className, $id)] ?? null;
    }

    /**
     * @throws Exception
     */
    protected static function addRecord(string $className, ?array $data): void {
        if (empty($data['id'])) {
            throw new Exception("id должно быть заполненым");
        }

        $id = $data['id'];

        $record = static::getRecord($className, $id);

        if (empty($record)) {
            static::$identityMap[self::getRecordKey($className, $id)] = $data;
        }
    }

    private static function getRecordKey(string $className, $id): string {
        return "$className.$id";
    }

    public function getDB(): DB {
        return $this->db;
    }
}