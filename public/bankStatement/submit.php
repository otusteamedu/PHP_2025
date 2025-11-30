<?php
declare(strict_types=1);

require '../../vendor/autoload.php';
require '../../app/bootstrap.php';

use App\Classes\QueueManager;
use App\Classes\StatusStore;

$requestId = uniqid('req_', true);

$statusStore = new StatusStore();
$statusStore->set($requestId, 'processing');

QueueManager::publish(
    [
        'requestId' => $requestId,
        'from' => $_POST['from'],
        'to' => $_POST['to'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone']
    ]
);

header('Content-Type: application/json');

echo json_encode(['requestId' => $requestId], JSON_THROW_ON_ERROR);
