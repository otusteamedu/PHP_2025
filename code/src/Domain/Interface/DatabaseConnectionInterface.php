<?php

declare(strict_types=1);

namespace MkdBot\Domain\Interface;

use PDO;

/**
 * Интерфейс подключения к БД — абстракция для будущей миграции на YDB Serverless (v2)
 */
interface DatabaseConnectionInterface
{
    /**
     * Возвращает экземпляр PDO для работы с БД
     */
    public function getConnection(): PDO;

    /**
     * Проверяет доступность подключения
     */
    public function isAvailable(): bool;

    /**
     * Переподключение к БД — пересоздаёт PDO instance
     */
    public function reconnect(): PDO;

    /**
     * Проверяет, живо ли соединение, и при необходимости переподключается.
     * Используется в долгоживущих consumer-процессах перед каждой операцией с БД.
     */
    public function ensureConnection(): PDO;
}
