<?php
require '../vendor/autoload.php';
require '../app/bootstrap.php';

use App\Classes\QueueManager;
use App\Classes\StatusStore;

$requestId = uniqid('req_', true);
//requestId нужен будет чтобы далее отслеживать статус

$data = [
    'requestId' => $requestId,
    'from' => $_POST['from'],
    'to' => $_POST['to'],
    'email' => $_POST['email'],
    'phone' => $_POST['phone']
];

// переключаем статус на "в обработке"
$statusStore = new StatusStore();
$statusStore->set($requestId, 'processing');

// отправляем задачу в очередь
QueueManager::publish($data);

// отдаём requestId клиенту чтобы на фронтэнде тоже можно было отслеживать статус
header('Content-Type: application/json');
echo json_encode(['requestId' => $requestId]);

