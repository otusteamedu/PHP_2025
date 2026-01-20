<?php

namespace App\Controller;

use App\Service\TelegramBot;
use App\Controller\StateController;
class BotController
{
    private TelegramBot $bot;
    private $message;

    public function __construct(TelegramBot $bot)
    {
        $input = file_get_contents('php://input');
        $currMessage = json_decode($input, true);
        $this->bot = $bot;
        $this->message = $currMessage;
    }
    public function checkPermissions() {
        if (empty($this->message)) {
            http_response_code(400);
            echo 'Invalid request';
            return;
        }
        $result = $this->bot->checkPermissions($this->message);

        return $result;

    }

    public function getCurrentUser() { 
        
        if (empty($this->message)) {
            http_response_code(400);
            echo 'Invalid request';
            return;
        }
        $result = $this->bot->getCurrentUser($this->message);

        return $result;
    }

    public function getCurrentState() { 
            
        if (empty($this->message)) {
            http_response_code(400);
            echo 'User is empty';
            return;
        }
        // file_put_contents('./logs.log', print_r($this->message, true), FILE_APPEND);
        $user = $this->getCurrentUser();
        
        
        if (empty($user)) {
            http_response_code(400);
            echo 'User is empty';
            return;
        }
       
        $userId = $user->getId();
        $stateController = new StateController();
        
        $currState = $stateController->getUserState($userId);
        // file_put_contents('./logs.log', $currState, FILE_APPEND);

       
        return $currState;
    }

    public function setCurrentUserStartState() {
        if (empty($this->message)) {
            http_response_code(400);
            echo 'Message is empty';
            return;
        }
        
        $user = $this->getCurrentUser();
        if (empty($user)) {
            http_response_code(400);
            echo 'User is empty';
            return;
        }
        $userId = $user->getId();
        $stateController = new StateController();
        $stateController->setUserState($userId, "start");
        
    }

    public function setCurrentUserChangeCountDealState($state) {
        file_put_contents('./logs.log', print_r($state, true), FILE_APPEND);
        if (empty($this->message)) {
            http_response_code(400);
            echo 'Message is empty';
            return;
        }
        
        $user = $this->getCurrentUser();
        if (empty($user)) {
            http_response_code(400);
            echo 'User is empty';
            return;
        }
        
        $userId = $user->getId();
        $stateController = new StateController();
        $stateController->setUserState($userId, $state);
        
    }

    public function sendStartMessage(){
        if (empty($this->message)) {
            http_response_code(400);
            echo 'Message is empty';
            return;
        }
        
        $user = $this->getCurrentUser();
        if (empty($user)) {
            http_response_code(400);
            echo 'User is empty';
            return;
        }
        $this->bot->sendStartMessage($this->message, $user);
    }

    public function checkDealsRequest(){
        if (empty($this->message)) {
            http_response_code(400);
            echo 'Message is empty';
            return;
        }
        
        $user = $this->getCurrentUser();
        if (empty($user)) {
            http_response_code(400);
            echo 'User is empty';
            return;
        }
        
        $isSendedMessageDeals = $this->bot->checkDealsRequest($this->message, $user);
        if ($isSendedMessageDeals) {
            $stateController = new StateController();
            $userId = $user->getId();
            $stateController->setUserState($userId, "deals");
        }
    }

    public function getDeals() {
        
        if (empty($this->message)) {
            http_response_code(400);
            echo 'Message is empty';
            return;
        }
        
        $user = $this->getCurrentUser();
        if (empty($user)) {
            http_response_code(400);
            echo 'User is empty';
            return;
        }
        $userBitrixId = $user->getBitrixId();
        
        $this->bot->getDeals($userBitrixId, $this->message);
    }

    public function checkDealOptionCallbackRequest() {
        // file_put_contents('./logs.log', print_r($this->message, true), FILE_APPEND);
        $result = null;
        if (empty($this->message)) {
            http_response_code(400);
            echo 'Message is empty';
            return;
        }
        $user = $this->getCurrentUser();
        if (empty($user)) {
            http_response_code(400);
            echo 'User is empty';
            return;
        }
        if (isset($this->message["callback_query"])) {
            $result = $this->bot->processDealOptionCallback($this->message, $user);
        } else {
            $this->bot->sendRemovingHelpMessage($this->message, "Пожалуйста выберите сделку", 5);
        }
        return $result;
    }

    public function checkChangeCountDealRequest($currState) {
        $result = null;
        if (empty($this->message)) {
            http_response_code(400);
            echo 'Message is empty';
            return;
        }
        $user = $this->getCurrentUser();
        if (empty($user)) {
            http_response_code(400);
            echo 'User is empty';
            return;
        }
        $result = $this->bot->processCountDealMessage($this->message, $user, $currState);
        if ($result) {
            $this->bot->showDealDefaultMessage($this->message,$result);
            // file_put_contents('./logs.log', print_r($user, true), FILE_APPEND);
            $userId = $user->getId();
            $stateController = new StateController();
            $stateController->setUserState($userId, "deals");
        }
    }
    // public function handleWebhook(): void
    // {
    //     try {
    //         $input = file_get_contents('php://input');
    //         $update = json_decode($input, true);

    //         if (empty($update)) {
    //             http_response_code(400);
    //             echo 'Invalid request';
    //             return;
    //         }

    //         // Логируем входящий запрос
    //         error_log('Telegram Update: ' . print_r($update, true));

    //         // Обрабатываем обновление
    //         $this->bot->handleUpdate($update);

    //         echo 'OK';
            
    //     } catch (\Exception $e) {
    //         error_log('Bot error: ' . $e->getMessage());
    //         http_response_code(500);
    //         echo 'Internal server error';
    //     }
    // }

    // public function setWebhook(): void
    // {
    //     header('Content-Type: application/json');
    //     $result = $this->bot->setWebhook();
    //     echo json_encode($result, JSON_PRETTY_PRINT);
    // }

    // public function deleteWebhook(): void
    // {
    //     header('Content-Type: application/json');
    //     $result = $this->bot->deleteWebhook();
    //     echo json_encode($result, JSON_PRETTY_PRINT);
    // }

    // public function test(): void
    // {
    //     echo json_encode([
    //         'status' => 'ok',
    //         'message' => 'Bot is running',
    //         'timestamp' => date('Y-m-d H:i:s')
    //     ], JSON_PRETTY_PRINT);
    // }
}