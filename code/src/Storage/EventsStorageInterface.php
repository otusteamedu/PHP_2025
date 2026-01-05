<?php

declare(strict_types=1);

namespace App\Storage;

interface EventsStorageInterface
{
    /**
     * Сохраняет событие
     * @param array $event Событие
     * @return void
     */
    public function addEvent(array $event): void;

    /**
     * Удаляет все события
     * @return void
     */
    public function clear(): void;

    /**
     * Возвращает все подходящие события, отсортированные по приоритету (desc)
     * @param array $params Параметры для поиска
     * @return array
     */
    public function findMatches(array $params): array;
}
