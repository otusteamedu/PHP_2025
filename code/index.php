<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Application\Validator;
use App\Infrastructure\ApiService;
use App\Infrastructure\MockOrderRepository;
use App\Presentation\OrderController;

// Получаем данные из POST-запроса
$orderData = json_decode(file_get_contents('php://input'), true);

// Создаем экземпляры классов
$validator = new Validator();
$apiService = new ApiService();
$orderRepository = new MockOrderRepository();
$controller = new OrderController($validator, $apiService, $orderRepository);

// Обрабатываем заказ
$controller->processOrder($orderData);
