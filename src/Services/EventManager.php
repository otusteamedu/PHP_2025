<?php

namespace App\Services;

use App\Interfaces\EventStorageInterface;

/**
 * Менеджер событий
 */
class EventManager
{
    /**
     * EventStorageInterface $storage Хранилище событий
     */
    public function __construct(private EventStorageInterface $storage) {}

    /**
     * Добавить новое событие в систему
     */
    public function addEvent(int $priority, array $conditions, array $eventData): string
    {
        // Валидация условий
        foreach ($conditions as $condition) {
            if (strpos($condition, '=') === false) {
                throw new \InvalidArgumentException(
                    "Condition must be in format 'param = value', got: " . $condition
                );
            }
        }
        
        // Создаем объект события и сохраняем в хранилище
        $event = new \App\Models\Event($priority, $conditions, $eventData);
        return $this->storage->add($event);
    }

    /**
     * Очистить все события в системе
     * bool Успех операции
     */
    public function clearEvents(): bool
    {
        return $this->storage->clearAll();
    }

    /**
     * Найти наиболее подходящее событие по параметрам запроса
     * Выбирает событие, удовлетворяющее по всем условиям, с максимальным приоритетом
     * $params Параметры запроса в формате ["param1" => "1", "param2" => "2"]
     * array|null Данные найденного события или nullх
     */
    public function findBestEvent(array $params): ?array
    {
        // Ищем событие в хранилище
        $event = $this->storage->findBestMatch($params);
        
        // Если событие не найдено
        if (!$event) {
            return null;
        }

        // Возвращаем данные события в формате, готовом для отправки
        return [
            'priority' => $event->priority,
            'conditions' => $event->conditions,
            'event' => $event->eventData,
            'id' => $event->id,
        ];
    }

    /**
     * Получить все события из системы
     * @return array Массив всех событий
     */
    public function getAllEvents(): array
    {
        $events = $this->storage->getAll();
        
        return array_map(
            fn($event) => [
                'id' => $event->id,
                'priority' => $event->priority,
                'conditions' => $event->conditions,
                'event' => $event->eventData,
            ],
            $events
        );
    }
}