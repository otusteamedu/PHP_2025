<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Aovchinnikova\Hw15\Controller\EmailController;

$controller = new EmailController();

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($path) {
    case '/validate':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->handleValidationRequest();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    case '/status':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $requestId = $_GET['request_id'] ?? '';
            if (empty($requestId)) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing request_id parameter']);
            } else {
                $controller->checkRequestStatus($requestId);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    case '/process-queue':
        // This would typically be run as a separate worker process
        $controller->processQueue();
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
        break;
}
