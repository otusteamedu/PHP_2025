<?php

namespace App\Services;

use App\Exceptions\TemporaryProcessingException;

class QueueService
{
    private $connection;
    private $channel;
    private string $queueName;

    public function __construct()
    {
        $host = $this->envString('AMQP_HOST', 'rabbitmq');
        $port = $this->envInt('AMQP_PORT', 5672);
        $user = $this->envString('AMQP_USER', '');
        $password = $this->envString('AMQP_PASSWORD', '');
        $vhost = $this->envString('AMQP_VHOST', '/');
        $this->queueName = $this->envString('AMQP_QUEUE_NAME', 'task_queue');

        if ($user === '' || $password === '') {
            throw new \InvalidArgumentException('AMQP_USER and AMQP_PASSWORD must be set in environment variables.');
        }

        try {
            $connectionClass = '\\PhpAmqpLib\\Connection\\AMQPStreamConnection';
            if (!class_exists($connectionClass)) {
                throw new \RuntimeException('php-amqplib is not installed. Run composer install in src/.');
            }

            $this->connection = new $connectionClass($host, $port, $user, $password, $vhost);
            $this->channel = $this->connection->channel();
            

            $this->channel->queue_declare($this->queueName, false, true, false, false);
        } catch (\Exception $e) {
   
        throw new \Exception("Could not connect to RabbitMQ: " . $e->getMessage());
        }
    }

    public function push(array $data): void
    {
        $messageClass = '\\PhpAmqpLib\\Message\\AMQPMessage';
        if (!class_exists($messageClass)) {
            throw new \RuntimeException('php-amqplib is not installed. Run composer install in src/.');
        }

        $payload = json_encode($data);
        $msg = new $messageClass(
            $payload,
            // 2 = persistent delivery mode in AMQP
            ['delivery_mode' => 2]
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

            if (!is_array($data)) {
                echo " [!] Invalid JSON payload, message acknowledged\n";
                $msg->ack();
                return;
            }
            
            echo " [x] Received task\n";

            try {
                // Выполняем логику обработчика
                call_user_func($callback, $data);
                
                // Подтверждаем сообщение только после успешной обработки
                $msg->ack();
                echo " [x] Done\n";
            } catch (TemporaryProcessingException $e) {
                echo " [~] Temporary failure, requeue message: " . $e->getMessage() . "\n";
                $msg->nack(false, true);
            } catch (\Throwable $e) {
                echo " [!] Permanent error, message acknowledged: " . $e->getMessage() . "\n";
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

    private function envString(string $key, string $default): string
    {
        $value = getenv($key);

        if ($value === false || $value === '') {
            return $default;
        }

        return $value;
    }

    private function envInt(string $key, int $default): int
    {
        $value = getenv($key);

        if ($value === false || $value === '') {
            return $default;
        }

        if (!is_numeric($value)) {
            throw new \InvalidArgumentException(sprintf('Environment variable %s must be numeric.', $key));
        }

        return (int) $value;
    }
}
