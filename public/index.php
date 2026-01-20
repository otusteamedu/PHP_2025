<?php

require_once __DIR__ . '/../vendor/autoload.php';
use App\Database\Database;
use App\Controller\BotController;
use App\Controller\UserApiController;

use App\Service\TelegramBot;
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $_ENV[$name] = $value;
    }
}
$botToken = $_ENV['BOT_TOKEN'] ?? getenv('BOT_TOKEN');
$bitrixUrl = $_ENV['BITRIX_URL'] ?? getenv('BITRIX_URL');
// file_put_contents('./logs.log', print_r($botToken, true), FILE_APPEND);
// // Определяем хост
$input = file_get_contents('php://input');
$currMessage = json_decode($input, true);
// 
$host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
$webhookUrl = $protocol . '://' . $host;
// Создаем экземпляры
$bot = new TelegramBot($botToken, $webhookUrl, $bitrixUrl);
$controller = new BotController($bot);
// $controller->checkDealOptionCallbackRequest();

$response = $controller->checkPermissions();
if ($response) {
    $currState = $controller->getCurrentState();
    if ( $currState === null ) {
        $controller->setCurrentUserStartState();
        $controller->sendStartMessage();
    } else if ($currState === "start") {
        $controller->checkDealsRequest();
    } else if ($currState === "deals") {
        $result = $controller->checkDealOptionCallbackRequest();
        // file_put_contents('./logs.log', print_r($result, true), FILE_APPEND);
        if ($result != null) {
            // file_put_contents('./logs.log', print_r($result, true), FILE_APPEND);
            $controller->setCurrentUserChangeCountDealState($result);
        }
    } else if (str_contains($currState , "change_count_deal")) {
        $controller->checkChangeCountDealRequest($currState);
    }
    
}
// // Загружаем переменные окружения
// $envFile = __DIR__ . '/../.env';
// if (file_exists($envFile)) {
//     $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
//     foreach ($lines as $line) {
//         if (strpos(trim($line), '#') === 0) continue;
        
//         list($name, $value) = explode('=', $line, 2);
//         $_ENV[$name] = $value;
//     }
// }

// // Получаем токен бота
// $botToken = $_ENV['BOT_TOKEN'] ?? getenv('BOT_TOKEN');
// $dbPath = $_ENV['DB_PATH'] ?? getenv('DB_PATH') ?? __DIR__ . '/../data/bot_database.sqlite';

// if (!$botToken) {
//     die('BOT_TOKEN is not set in environment variables');
// }

// // Создаем директорию для базы данных если не существует
// $dbDir = dirname($dbPath);
// if (!is_dir($dbDir)) {
//     mkdir($dbDir, 0755, true);
// }

// // Определяем протокол (HTTP/HTTPS) с учетом SSL
// $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
//            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
//            (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https');
// $protocol = $isHttps ? 'https' : 'http';


// $controller = new BotController($bot);

// // Обрабатываем запрос
// $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// switch ($requestUri) {
//     case '/set-webhook':
//         $controller->setWebhook();
//         break;
        
//     case '/delete-webhook':
//         $controller->deleteWebhook();
//         break;
        
//     case '/test':
//         $controller->test();
//         break;
        
//     case '/ssl-test':
//         // Тестовый эндпоинт для проверки SSL
//         echo json_encode([
//             'status' => 'ok',
//             'ssl_enabled' => $isHttps,
//             'protocol' => $protocol,
//             'host' => $host,
//             'webhook_url' => $webhookUrl,
//             'timestamp' => date('Y-m-d H:i:s')
//         ], JSON_PRETTY_PRINT);
//         break;
        
//     case '/':
//     default:
//         $controller->handleWebhook();
//         break;
// }