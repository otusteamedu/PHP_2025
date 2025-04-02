<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

/**
 * Class PdoBuilder
 * @package App\Database
 */
class PdoBuilder
{
    /**
     * @param Config $config
     * @return PDO
     */
    public static function create(Config $config): PDO
    {
        $dsn = sprintf(
            "pgsql:host=%s;port=%d;dbname=%s",
            $config->getHost(),
            $config->getPort(),
            $config->getDbName()
        );

        $pdo = new PDO($dsn, $config->getUserName(), $config->getPassword());
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    }
}
