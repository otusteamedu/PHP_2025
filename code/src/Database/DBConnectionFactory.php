<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

/**
 * Фабрика подключения к базе данных Postgres
 */
final class DBConnectionFactory
{
    /**
     * Создает PDO-подключение по настройкам окружения
     *
     * @return PDO
     */
    public function create(): PDO
    {
        $host = getenv('POSTGRES_HOST') ?: 'postgres';
        $port = getenv('POSTGRES_PORT') ?: '5432';
        $database = getenv('POSTGRES_DB') ?: 'database_requests';
        $user = getenv('POSTGRES_USER') ?: 'postgres';
        $password = getenv('POSTGRES_PASSWORD') ?: 'postgres';

        $dsn = 'pgsql:host=' . $host . ';port=' . $port . ';dbname=' . $database;

        return new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}
