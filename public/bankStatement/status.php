<?php
declare(strict_types=1);

require '../../vendor/autoload.php';
require '../../app/bootstrap.php';

use App\Classes\StatusStore;

$requestId = $_POST['request_id'] ?? null;

if (empty($requestId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Отсутствует номер заявки'], JSON_THROW_ON_ERROR);
    exit;
}

$statusStore = new StatusStore();
$status = $statusStore->get($requestId);

echo $status ?? 'unknown';