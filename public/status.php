<?php

$taskId = $_GET['task_id'] ?? null;

if (!$taskId) {
    http_response_code(400);
    exit('task_id обязателен');
}

$taskFile = __DIR__ . '/../tasks/' . $taskId . '.json';

if (!file_exists($taskFile)) {
    http_response_code(404);
    exit('Задача не найдена');
}

$data = json_decode(file_get_contents($taskFile), true);
header('Content-Type: application/json');
echo json_encode(['status' => $data['status'] ?? 'unknown']);
