<?php

namespace App\Queue;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class Consumer {
    public function listen() {
        $config = require __DIR__ . '/../../config/rabbitmq.php';
        $connection = new AMQPStreamConnection(...array_values($config));
        $channel = $connection->channel();

        $channel->queue_declare($config['queue'], false, true, false, false);

        $callback = function($msg) {
            $data = json_decode($msg->body, true);
            $taskId = $data['task_id'] ?? null;

            echo "Обработка задачи: $taskId\n";
            echo "Получен запрос: {$data['start_date']} - {$data['end_date']}\n";

            $taskFile = __DIR__ . "/../../tasks/{$taskId}.json";
            file_put_contents($taskFile, json_encode(['status' => 'processing']));

            sleep(5); // имитация долгой работы

            file_put_contents($taskFile, json_encode(['status' => 'done']));
        };

        $channel->basic_consume($config['queue'], '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }
}

