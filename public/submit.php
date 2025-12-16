<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Queue\Producer;

$start = $_POST['start_date'] ?? null;
$end = $_POST['end_date'] ?? null;

if (!$start || !$end) {
    http_response_code(400);
    exit(json_encode(['error' => 'Неверные данные']));
}

$taskId = uniqid('task_', true);

$data = [
    'task_id' => $taskId,
    'start_date' => $start,
    'end_date' => $end,
];

// Сохраняем статус
file_put_contents(__DIR__ . "/../tasks/{$taskId}.json", json_encode(['status' => 'pending']));

// Отправляем в очередь
$producer = new Producer();
$producer->send($data);

header('Content-Type: application/json');
echo json_encode(['task_id' => $taskId]);
