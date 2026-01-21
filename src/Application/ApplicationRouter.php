<?php
namespace App\Application;
use App\Database\Database;
use App\Controller\BotController;
use App\Controller\UserApiController;
use App\Service\TelegramBot;

class ApplicationRouter {
    public static function init($method, $path, $env) {
        switch (true) {
            case $method === 'DELETE' && preg_match('#^/api/users/bitrix/(\d+)$#', $path, $matches):
                self::ProcessRemoveUserRoute($matches);
                break;
                
            case $method === 'POST' && $path === '/api/users':
                self::ProcessRemoveUserRoute($matches);
                break;
            
            case $method === 'POST' && $path === '/': 
                self::ProcessTelegramMessage($env);
                break;
            case $method === 'PUT' && preg_match('#^/api/users/bitrix/(\d+)$#', $path, $matches):
                self::ProcessRemoveUserRoute($matches);
                break;
            }
    }
    
    private static function ProcessUpdateUserRoute($matches){
        $bitrixId = (int)$matches[1];
        $data = json_decode(file_get_contents('php://input'), true);
        $response = $controller->updateByBitrixId($bitrixId, $data);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    private static function ProcessRemoveUserRoute($matches){
        $controller = new UserApiController();
        $bitrixId = (int)$matches[1];
        $response = $controller->deleteByBitrixApi($bitrixId);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    private static function ProcessAddUserRoute(){
        $controller = new UserApiController();
        $data = json_decode(file_get_contents('php://input'), true);
        $response = $controller->create($data);
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    private static function ProcessTelegramMessage($env) {
        
        $botToken = $env['BOT_TOKEN'] ?? getenv('BOT_TOKEN');
        $bitrixUrl = $env['BITRIX_URL'] ?? getenv('BITRIX_URL');
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
    }
}
