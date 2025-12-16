<?php

namespace App\Queue;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Producer {
    public function send(array $data) {
        $config = require __DIR__ . '/../../config/rabbitmq.php';
        $connection = new AMQPStreamConnection(...array_values($config));
        $channel = $connection->channel();

        $channel->queue_declare($config['queue'], false, true, false, false);

        $msg = new AMQPMessage(json_encode($data), ['delivery_mode' => 2]);
        $channel->basic_publish($msg, '', $config['queue']);

        $channel->close();
        $connection->close();
    }
}
