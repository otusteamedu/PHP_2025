<?php

namespace Arlex2305k\PatternsDb;

interface IMapperRegistry
{
    public static function getIdentityMap(): IdentityMap;

    public function getConnection(): \PDO;

    public function setConnection(\PDO $connection): void;
}
