<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class QueueService
{
    private $connection;
    private $channel;
    private string $queueName = 'task_queue';

    public function __construct()
    {

        try {
            $this->connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
            $this->channel = $this->connection->channel();
            

            $this->channel->queue_declare($this->queueName, false, true, false, false);
        } catch (\Exception $e) {
   
        throw new \Exception("Could not connect to RabbitMQ: " . $e->getMessage());
        }
    }

    public function push(array $data): void
    {
        $payload = json_encode($data);
        $msg = new AMQPMessage(
            $payload,
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );
        
        $this->channel->basic_publish($msg, '', $this->queueName);
    }

    /**
     * Запускает чтение сообщений из очереди.
     * Метод блокирует выполнение на неопределенное время.
     *
     * @param callable $callback Функция обработки данных сообщения (array)
     */
    public function consume(callable $callback): void
    {
        echo "Waiting for messages. To exit press CTRL+C\n";

        // $callbackWrapper принимает AMQP-сообщение, декодирует тело, вызывает пользовательский callback и подтверждает обработку
        $callbackWrapper = function ($msg) use ($callback) {
            $body = $msg->body;
            $data = json_decode($body, true);
            
            echo " [x] Received task\n";

            try {
                // Выполняем логику обработчика
                call_user_func($callback, $data);
                
                // Подтверждаем сообщение только после успешной обработки
                $msg->ack();
                echo " [x] Done\n";
            } catch (\Exception $e) {
                echo " [!] Error processing message: " . $e->getMessage() . "\n";
                // В реальном приложении здесь обычно делают ретраи или отправку в dead letter queue.
                // В этом демо подтверждаем сообщение, чтобы избежать бесконечных повторных доставок.
                $msg->ack(); 
            }
        };

        // Равномерная выдача: не отдавать одному воркеру больше одного сообщения за раз
        $this->channel->basic_qos(null, 1, null);
        
        // basic_consume(queue, consumer_tag, no_local, no_ack, exclusive, nowait, callback)
        // no_ack = false означает, что подтверждение нужно отправлять вручную (это делается в callbackWrapper)
        $this->channel->basic_consume($this->queueName, '', false, false, false, false, $callbackWrapper);

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function close(): void
    {
        try {
            if ($this->channel) {
                $this->channel->close();
            }
            if ($this->connection) {
                $this->connection->close();
            }
        } catch (\Exception $e) {
            // Игнорируем ошибки при закрытии соединения
        }
    }
    
    public function __destruct()
    {
        $this->close();
    }
}
