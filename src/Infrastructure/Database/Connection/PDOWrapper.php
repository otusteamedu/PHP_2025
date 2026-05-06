<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Connection;

use App\Core\Config\DotEnvLoader;

class PDOWrapper
{
    private static ?self $dbInstance = null;
    private static \PDO $dbh;

    private function __construct()
    {
        $dotEnvLoader = new DotEnvLoader();

        $host = $dotEnvLoader->getEnv('POSTGRES_HOST');
        $dbName = $dotEnvLoader->getEnv('POSTGRES_DB');
        $user = $dotEnvLoader->getEnv('POSTGRES_USER');
        $password = $dotEnvLoader->getEnv('POSTGRES_PASSWORD');

        self::$dbh = new \PDO("pgsql:host=$host;dbname=$dbName", $user, $password);
    }

    public static function getHandler(): \PDO
    {
        if (self::$dbInstance === null) {
            self::$dbInstance = new self();
        }

        return self::$dbInstance::$dbh;
    }
}
