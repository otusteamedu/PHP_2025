<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Domain\Entity\Task;
use App\Domain\Queue\TaskQueueInterface;

final class QueueController
{
    public function __construct(private TaskQueueInterface $queue)
    {
    }

    public function handle(): string
    {
        $successMessage = '';
        $errorMessage = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email']??'';
            // валидация email;
            $task = new Task($email);

            $this->queue->push($task);

            $successMessage = 'Запрос принят в обработку.';
        }

        ob_start();

        require __DIR__ . '/../../Presentation/View/form.php';

        return (string)ob_get_clean();
    }
}