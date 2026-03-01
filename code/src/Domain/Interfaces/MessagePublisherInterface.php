<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

interface MessagePublisherInterface
{
    /**
     * Публикует сообщение в очередь
     *
     * @param string $queue Имя очереди
     * @param array $message Данные сообщения
     * @param string $exchange Имя обменника (по умолчанию '')
     * @param string $routingKey Routing key (по умолчанию имя очереди)
     */
    public function publish(string $queue, array $message, string $exchange = '', string $routingKey = ''): void;
}
