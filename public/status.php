<?php
require '../vendor/autoload.php';
require '../app/bootstrap.php';

use App\Classes\StatusStore;

// получаем requestId из запроса
$requestId = $_GET['id'] ?? null;

if (!$requestId) {
    http_response_code(400);
    echo json_encode(['error' => 'Отсутствует номер заявки']);
    exit;
}

$statusStore = new StatusStore();
$status = $statusStore->get($requestId);

echo $status ?? 'unknown';