<?php

namespace App\Interfaces;

use App\Models\Event;

/**
 * Интерфейс для хранилища событий
 */
interface EventStorageInterface
{
    /**
     * Добавить новое событие в хранилище
     */
    public function add(Event $event): string;

    /**
     * Получить все события из хранилища
     */
    public function getAll(): array;

    /**
     * Очистить все события из хранилища
     */
    public function clearAll(): bool;

    /**
     * Найти событие по параметрам запроса
     */
    public function findBestMatch(array $params): ?Event;
}