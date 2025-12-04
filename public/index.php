<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\EmailVerificationController;

$controller = EmailVerificationController::create();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = trim($_POST['emails'] ?? '');
    $emails = array_filter(array_map('trim', explode(PHP_EOL, $input)));
    $result = $controller->verifyEmails($emails);
    header('Content-Type: application/json;');
    echo json_encode($result);
} else {
    require('verify.html');
}
