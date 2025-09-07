<?php

namespace App\Infrastructure\Queue;

use App\Application\Services\TaskService;
use App\Application\Services\TodoService;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitmqReceiver
{
    private $host;
    private $port;
    private $username;
    private $password;

    public function __construct(private TodoService $todoService, private TaskService $taskService)
    {
        $this->host = getenv('RABBITMQ_HOST');
        $this->port = getenv('RABBITMQ_PORT');
        $this->username = getenv('RABBITMQ_USERNAME');
        $this->password = getenv('RABBITMQ_PASSWORD');
    }

    public function runReceiver(): void
    {
        $connection = new AMQPStreamConnection($this->host, $this->port, $this->username, $this->password);
        $channel = $connection->channel();

        $channel->queue_declare('todos', false, false, false, false);

        $callback = function (AMQPMessage $msg) {
            $message = $msg->getBody();
            $data = json_decode($message, true);
            $userId = $data['user_id'];
            $type = $data['type'];
            $taskId = $data['task_id'];

            echo " [x] Received task\n";
            echo " [x] TaskId: $taskId\n";
            echo " [x] UserId: $userId\n";
            echo " [x] Type: $type\n\n";

            switch ($type) {
                case 'add':
                    $this->todoService->add($data['content'], $userId);
                    $this->taskService->completeTask($taskId, $userId);
                    break;
                case 'update':
                    $this->todoService->update($data['content'], $data['todo_id'], $userId);
                    $this->taskService->completeTask($taskId, $userId);
                    break;
                case 'delete':
                    $this->todoService->delete($data['todo_id'], $userId);
                    $this->taskService->completeTask($taskId, $userId);
                    break;
                default:
                    echo " [x] Error: Unknown type of task\n\n";
            }
        };

        $channel->basic_consume('todos', '', false, true, false, false, $callback);

        try {
            $channel->consume();
        } catch (\Throwable $exception) {
            echo $exception->getMessage();
        }
    }
}