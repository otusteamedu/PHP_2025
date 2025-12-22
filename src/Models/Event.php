<?php

namespace App\Models;

/**
 * Модель события
 */
class Event
{
    /**
     * $priority Приоритет события (чем больше, тем выше приоритет)
     * $conditions Условия возникновения в формате ["param1 = 1", "param2 = 2"]
     * $eventData Данные события для отправки сервису событий
     * $id Уникальный идентификатор события
     */
    public function __construct(
        public readonly int $priority,
        public readonly array $conditions, // Формат: ["param1 = 1", "param2 = 2"]
        public readonly array $eventData,
        public readonly ?string $id = null
    ) {}

    /**
     * Сравнивает каждый параметр из запроса с условиями события
     * Все условия должны быть выполнены
     * $params Параметры запроса в формате ["param1" => "1", "param2" => "2"]
     * bool true если все условия выполнены
     */
    public function matches(array $params): bool
    {
        // Проверяем каждое условие события
        foreach ($this->conditions as $condition) {
            // Условие "param1 = 1" на ключ и значение
            [$key, $value] = explode('=', trim($condition));
            $key = trim($key);
            $value = trim($value);
            
            // Если параметр не передан или не совпадает - условие не выполнено
            if (!isset($params[$key]) || trim($params[$key]) != $value) {
                return false;
            }
        }
        
        // Все условия выполнены
        return true;
    }

    /**
     * Создать событие из массива данных
     * $data Данные события
     * Event Объект события
     */
    public static function fromArray(array $data): Event
    {
        return new self(
            $data['priority'],
            $data['conditions'],
            $data['eventData'],
            $data['id'] ?? null
        );
    }

    /**
     * Преобразовать событие в массив
     * return Массив данных события
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'priority' => $this->priority,
            'conditions' => $this->conditions,
            'eventData' => $this->eventData,
        ];
    }
}