<?php

namespace App\Storage;

use App\Interfaces\EventStorageInterface;
use App\Models\Event;
use Redis;

/**
 * Реализация хранилища событий на Redis
 * Использует Redis Hash для хранения событий
 */
class RedisStorage implements EventStorageInterface
{
    /**
     * Ключ Redis Hash для хранения всех событий
     */
    private const EVENTS_KEY = 'events';

    /**
     * $redis Экземпляр Redis клиента
     */
    public function __construct(private Redis $redis) {}

    /**
     * Добавить новое событие в Redis
     * Уникальный ID и сохраняет в Redis Hash
     */
    public function add(Event $event): string
    {
        // Генерируем уникальный ID для события
        $id = uniqid('event_', true);
        
        // Создаем событие с ID для хранения
        $eventToStore = new Event(
            $event->priority,
            $event->conditions,
            $event->eventData,
            $id
        );
        
        // Сохраняем в Redis Hash: ключ 'events', поле = event_id, значение = JSON
        $this->redis->hSet(
            self::EVENTS_KEY, 
            $id, 
            json_encode($eventToStore->toArray())
        );
        
        return $id;
    }

    /**
     * Получить все события из Redis
     * Читает все поля из Redis Hash
     */
    public function getAll(): array
    {
        $events = [];
        
        // Получаем все события из Redis Hash
        $allEvents = $this->redis->hGetAll(self::EVENTS_KEY);

        // Десериализуем события из JSON
        foreach ($allEvents as $eventJson) {
            $data = json_decode($eventJson, true);
            $events[] = Event::fromArray($data);
        }

        return $events;
    }

    /**
     * Очистить все события из Redis
     * Удаляет весь Hash с событиями
     */
    public function clearAll(): bool
    {
        return (bool) $this->redis->del(self::EVENTS_KEY);
    }

    /**
     * Ищет события, удовлетворяющие всем условиям, и выбирает с максимальным приоритетом
     * Параметры запроса в формате ["param1" => "1", "param2" => "2"]
     * Найденное событие или null
     */
    public function findBestMatch(array $params): ?Event
    {
        $allEvents = $this->getAll();
        $bestEvent = null;

        // Перебираем все события
        foreach ($allEvents as $event) {
            // Проверяем, удовлетворяет ли событие всем параметрам запроса
            if ($event->matches($params)) {
                // Выбираем событие с максимальным приоритетом
                if ($bestEvent === null || $event->priority > $bestEvent->priority) {
                    $bestEvent = $event;
                }
            }
        }

        return $bestEvent;
    }
}