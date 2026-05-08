<?php

declare(strict_types=1);

namespace App\Domain\Storage;

use App\Domain\Event\Event;

/**
 * Контракт хранилища событий, который позволяет заменить Redis на другую реализацию
 */
interface EventStorageInterface
{
    /**
     * Сохраняет событие в хранилище
     */
    public function add(Event $event): void;

    /**
     * Очищает все события в хранилище
     */
    public function clear(): void;

    /**
     * Ищет наиболее подходящее событие по параметрам пользователя
     *
     * @param array<string, mixed> $params
     */
    public function findEventByParamsWithMaxPriority(array $params): ?Event;
}
