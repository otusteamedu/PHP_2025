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
            $userId = $user->getId();
            $stateController = new StateController();
            $stateController->setUserState($userId, "deals");
        }
    }
}
