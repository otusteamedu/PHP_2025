<?php

namespace App;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Простой класс для работы с RabbitMQ
 * 
 * Зачем нужен: инкапсулирует всю сложность работы с очередью
 */
class Queue
{
    private $channel;
    private $queueName;

    public function __construct($queueName)
    {
        // Подключаемся к RabbitMQ
        $connection = new AMQPStreamConnection(
            $_ENV['RABBITMQ_HOST'],
            5672,
            'guest',
            'guest'
        );
        
        $this->channel = $connection->channel();
        $this->queueName = $queueName;
        
        // Создаем очередь (если её нет)
        $this->channel->queue_declare($queueName, false, true, false, false);
    }

    /**
     * Отправить запрос в очередь
     */
    public function sendRequest(array $data): void
    {
        // Превращаем массив в JSON
        $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
        
        // Создаем сообщение для RabbitMQ
        $message = new AMQPMessage($jsonData, [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);
        
        // Отправляем
        $this->channel->basic_publish($message, '', $this->queueName);
    }

    /**
     * Получить и обработать все запросы из очереди
     * @param callable $callback Функция, которая будет вызвана для каждого запроса
     */
    public function consumeRequests(callable $callback): void
    {
        // Берем по одному сообщению за раз
        $this->channel->basic_qos(null, 1, null);
        
        // Начинаем слушать очередь
        $this->channel->basic_consume(
            $this->queueName,
            '',
            false,
            false,  // manual ack
            false,
            false,
            function($message) use ($callback) {
                // Декодируем JSON обратно в массив
                $data = json_decode($message->body, true);
                
                // Вызываем пользовательскую функцию
                $result = $callback($data);
                
                if ($result) {
                    // Успешно обработали - подтверждаем
                    $message->ack();
                } else {
                    // Ошибка - возвращаем в очередь
                    $message->nack(true);
                }
            }
        );
        
        // Бесконечный цикл ожидания сообщений
        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }
}