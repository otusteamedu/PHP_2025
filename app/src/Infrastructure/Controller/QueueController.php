<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Domain\Entity\Task;
use App\Domain\Queue\TaskQueueInterface;
use JsonException;

final readonly class QueueController
{
    public function __construct(
        private TaskQueueInterface $queue,
    )
    {
    }

    /**
     * @throws JsonException
     */
    public function handle(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            http_response_code(405);

            echo json_encode([
                'success' => false,
                'message' => 'Метод не поддерживается',
            ], JSON_THROW_ON_ERROR);

            return;
        }


        $email = trim($_POST['email'] ?? '');


        if ($email === '') {

            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Email обязателен',
            ], JSON_THROW_ON_ERROR);

            return;
        }


        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Некорректный email',
            ], JSON_THROW_ON_ERROR);

            return;
        }


        try {

            $task = new Task($email);

            $this->queue->push($task);

            echo json_encode([
                'success' => true,
                'message' => 'Запрос принят в обработку',
            ], JSON_THROW_ON_ERROR);

        } catch (\Throwable $exception) {

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => 'Не удалось поставить задачу в очередь',
            ], JSON_THROW_ON_ERROR);

        }
    }
}