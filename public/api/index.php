<?php

require_once __DIR__ . '/../../vendor/autoload.php';
use App\Database\Database;
use App\Controller\BotController;
use App\Controller\UserApiController;
use App\Service\TelegramBot;


// require_once 'UserApiController.php';
$controller = new UserApiController();
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch (true) {
    case $method === 'DELETE' && preg_match('#^/api/users/bitrix/(\d+)$#', $path, $matches):
        $bitrixId = (int)$matches[1];
        $response = $controller->deleteByBitrixApi($bitrixId);
        break;
        
    case $method === 'POST' && $path === '/api/users':
        $data = json_decode(file_get_contents('php://input'), true);
        file_put_contents('./logs.log', print_r($data, true), FILE_APPEND);
        $response = $controller->create($data);
        break;
        
    case $method === 'PUT' && preg_match('#^/api/users/bitrix/(\d+)$#', $path, $matches):
        $bitrixId = (int)$matches[1];
        $data = json_decode(file_get_contents('php://input'), true);
        $response = $controller->updateByBitrixId($bitrixId, $data);
        break;
        
    default:
        $response = ['success' => false, 'error' => 'Not found', 'status' => 404];
}

// http_response_code($response['status']);
header('Content-Type: application/json');
echo json_encode($response);

