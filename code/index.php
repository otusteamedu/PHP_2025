<?php

declare(strict_types=1);

// 1. Подключаем автозагрузчик Composer
require_once __DIR__ . '/vendor/autoload.php';

use Ak\Hw\Routing\Router;
use Ak\Hw\Controllers\UserReportController;

// 2. Получение данных запроса
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// 3. Инициализация и настройка роутера
$router = new Router();

// Регистрируем наш маршрут для POST-запроса
$router->add('POST', '/api/user-report', [UserReportController::class, 'handleReportRequest']);

// 4. Диспетчеризация запроса
try {
    $router->dispatch($uri, $method);
} catch (\Exception $e) {
    $code = $e->getCode() ?: 500;
    if ($code < 100 || $code > 599) {
        $code = 500;
    }
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
