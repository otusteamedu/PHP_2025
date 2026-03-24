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
        // Default RabbitMQ credentials are guest/guest
        try {
            $this->connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
            $this->channel = $this->connection->channel();
            
            // Declare the queue to ensure it exists
            // queue_declare(queue, passive, durable, exclusive, auto_delete)
            $this->channel->queue_declare($this->queueName, false, true, false, false);
        } catch (\Exception $e) {
            // In a real app, we should probably log this or handle retry logic
            // For now, we'll let it bubble up so the worker/producer fails fast if RMQ is down
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
     * Start consuming messages from the queue.
     * This method blocks indefinitely.
     * 
     * @param callable $callback Function to process the message data (array)
     */
    public function consume(callable $callback): void
    {
        echo "Waiting for messages. To exit press CTRL+C\n";

        // $callbackWrapper handles the AMQP message object, decodes body, calls user callback, and acks
        $callbackWrapper = function ($msg) use ($callback) {
            $body = $msg->body;
            $data = json_decode($body, true);
            
            echo " [x] Received task\n";

            try {
                // Execute the worker logic
                call_user_func($callback, $data);
                
                // Acknowledge the message only if processing succeeded
                $msg->ack();
                echo " [x] Done\n";
            } catch (\Exception $e) {
                echo " [!] Error processing message: " . $e->getMessage() . "\n";
                // In a real app, handle retry logic or dead letter queues.
                // We acknowledge here to prevent infinite delivery loops in this demo.
                $msg->ack(); 
            }
        };

        // Fair dispatch: don't give more than 1 message to a worker at a time
        $this->channel->basic_qos(null, 1, null);
        
        // basic_consume(queue, consumer_tag, no_local, no_ack, exclusive, nowait, callback)
        // no_ack = false means we must manually ack (which we do in callbackWrapper)
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
            // Ignore closure errors
        }
    }
    
    public function __destruct()
    {
        $this->close();
    }
}
