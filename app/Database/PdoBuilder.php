<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Database;

use PDO;
use PDOException;

class PdoBuilder
{
    public static function create(AbstractConfig $config): PDO
    {
        try {
            $dsn = sprintf(
                "%s:host=%s;port=%d;dbname=%s",
                $config::NAME_DATABASE_MANAGEMENT_SYSTEM,
                $config->getHost(),
                $config->getPort(),
                $config->getDatabase()
            );

            $pdo = new PDO($dsn, $config->getLogin(), $config->getPassword());
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Could not connect Pdo: ' . $e->getMessage());
        }

        return $pdo;
    }
}
