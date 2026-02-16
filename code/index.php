<?php

require_once __DIR__ . '/vendor/autoload.php';

use Ak\Hw\Application\Validator;
use Ak\Hw\Infrastructure\ApiService;
use Ak\Hw\Infrastructure\MockOrderRepository;
use Ak\Hw\Presentation\OrderController;

// Создаем экземпляры классов
$validator = new Validator();
$apiService = new ApiService();
$orderRepository = new MockOrderRepository();
$controller = new OrderController($validator, $apiService, $orderRepository);

// Определяем метод запроса
$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'POST') {
    // Если это POST, обрабатываем данные
    // Проверяем, пришли данные как JSON или как form-data
    if (str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
        $orderData = json_decode(file_get_contents('php://input'), true) ?? [];
    } else {
        $orderData = $_POST;
    }
    $controller->processOrder($orderData);
} else {
    // Если это GET, просто показываем форму
    $controller->showOrderForm();
}
