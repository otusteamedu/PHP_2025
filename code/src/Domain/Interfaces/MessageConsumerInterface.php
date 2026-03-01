<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

interface MessageConsumerInterface
{
    /**
     * Потребляет сообщения из очереди
     *
     * @param string $queue Имя очереди
     * @param callable $callback Функция обработки сообщения
     */
    public function consume(string $queue, callable $callback): void;

    /**
     * Получает одно сообщение из очереди
     *
     * @param string $queue Имя очереди
     * @return array|null Данные сообщения или null если очередь пуста
     */
    public function get(string $queue): ?array;
}
