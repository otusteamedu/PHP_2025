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
// // Определяем хост

$host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
$webhookUrl = $protocol . '://' . $host;
// Создаем экземпляры
$bot = new TelegramBot($botToken, $webhookUrl, $bitrixUrl);
$controller = new BotController($bot);

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
        if ($result != null) {
            $controller->setCurrentUserChangeCountDealState($result);
        }
    } else if (str_contains($currState , "change_count_deal")) {
        $controller->checkChangeCountDealRequest($currState);
    }
    
}
