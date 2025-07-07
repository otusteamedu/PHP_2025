<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Event;

interface EventStorageInterface
{
    /**
     * Добавить новое событие в систему хранения
     */
    public function addEvent(Event $event): bool;

    /**
     * Очистить все доступные события
     */
    public function clearAllEvents(): bool;

    /**
     * Найти наиболее подходящее событие по параметрам
     */
    public function findBestMatchingEvent(array $params): ?Event;

    /**
     * Получить все события
     */
    public function getAllEvents(): array;
} 