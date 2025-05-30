<?php
namespace Aovchinnikova\Hw15\Console;

require __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

class QueueConsumer
{
    public function consumeMessages(): \Generator
    {
        $connection = new AMQPStreamConnection(
            $_ENV['RABBITMQ_HOST'] ?? 'rabbitmq',
            $_ENV['RABBITMQ_PORT'] ?? 5672,
            $_ENV['RABBITMQ_USER'] ?? 'guest',
            $_ENV['RABBITMQ_PASS'] ?? 'guest'
        );

        $channel = $connection->channel();
        $channel->queue_declare('email_validation', false, true, false, false);

        while (true) {
            $message = $channel->basic_get('email_validation');
            
            if ($message === null) {
                yield null;
                continue;
            }

            yield $message->body;
            $channel->basic_ack($message->delivery_info['delivery_tag']);
        }
    }
}

$consumer = new QueueConsumer();
foreach ($consumer->consumeMessages() as $message) {
    if ($message !== null) {
        echo " [x] Received: {$message}\n";
    }
    sleep(1);
}
