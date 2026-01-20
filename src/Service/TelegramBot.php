<?php

namespace App\Service;

use App\Model\User;

class TelegramBot
{
    private string $token;
    private string $webhookUrl;
    private string $bitrixUrl;
    public function __construct(string $token, string $webhookUrl, string $bitrixUrl)
    {
        $this->token = $token;
        $this->webhookUrl = $webhookUrl;
        $this->bitrixUrl = $bitrixUrl;
    }

    public function setWebhook(): array
    {
        $url = "https://api.telegram.org/bot{$this->token}/setWebhook";
        $params = [
            'url' => $this->webhookUrl
        ];

        return $this->sendRequest($url, $params);
    }

    public function deleteWebhook(): array
    {
        $url = "https://api.telegram.org/bot{$this->token}/deleteWebhook";
        return $this->sendRequest($url);
    }

    public function sendMessage(int $chatId, string $text): array
    {
        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ];

        return $this->sendRequest($url, $params);
    }

    public function deleteMessage($message_id, $chat_id) {
        $url = "https://api.telegram.org/bot{$this->token}/deleteMessage";
        
        $params = [
            'chat_id' => $chat_id,
            'message_id' => $message_id
        ];
        
        return $this->sendRequest($url, $params);
    }

    public function sendMessageKeyboard(int $chatId, string $text, $reply_markup): array
    {
        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => $reply_markup,
            'parse_mode' => 'markdown'
        ];
        return $this->sendRequest($url, $params);
    }

    public function handleUpdate(array $update): void
    {
        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        }
    }


    public function getCurrentUser(array $message) {  
        
        if (isset($message["callback_query"])) { 
            $user = User::findByUsername($message["callback_query"]['message']['chat']['username']);
        } else {
            $user = User::findByUsername($message['message']['from']['username']);
        }       
        return $user;
    }

    public function checkPermissions(array $message): bool
    {
        $result = true;
        if (isset($message["callback_query"])) {
            $chatId = $message["callback_query"]["message"]["chat"]["id"];
            
            $user = User::findByUsername($message["callback_query"]['message']['chat']['username']);
            if (!$user) {
                $response = "Доступ запрещён";
                $result = false;  
                $this->sendMessage($chatId, $response);
            }  
        } else {
            $chatId = $message["message"]["chat"]["id"];
            
            $user = User::findByUsername($message['message']['from']['username']);
            if (!$user) {
                $response = "Доступ запрещён";
                $result = false;  
                $this->sendMessage($chatId, $response);
            }  
        }             
           
        return $result;
    }

    public function sendStartMessage($message, $user){
        $chatId = $message["message"]["chat"]["id"];
        $response = "Привет, {$user->getFIO()}! 👋\n\n";
        $response .= "Пожалуйста нажмите /deals, \n
чтобы загрузить сделки и начать работу";
        // $req = 
        $this->sendMessage($chatId, $response);
        // file_put_contents('./logs.log', print_r($req, true), FILE_APPEND);
    }

    public function sendRemovingHelpMessage($message, $text, $time) {
        $chatId = $message["message"]["chat"]["id"];
          
        $req = $this->sendMessage($chatId, $text);
        sleep($time);     
        $this->deleteMessage($req["result"]["message_id"] ,$chatId);
        $this->deleteMessage($message["message"]["message_id"] ,$chatId);
    }

    public function checkDealsRequest($message, $user){
        $text = $message["message"]["text"];
        $chatId = $message["message"]["chat"]["id"];
        $isSendedMessageDeals = true;
        if ($text == "/deals") {
            $this->getDeals($user->getBitrixId(), $message);
        } else {
            $response = "Пожалуйста нажмите /deals,\nчтобы загрузить сделки и начать работу";
            $this->sendMessage($chatId, $response);
            $isSendedMessageDeals = false;
        }
        return $isSendedMessageDeals;
    }

    public function processCountDealMessage($message, $user,$currState) {
        
        $chatId = $message["message"]["chat"]["id"];
        $messageId = $message["message"]["message_id"];
        // $this->deleteMessage($messageId ,$chatId);
        if (filter_var($message["message"]["text"], FILTER_VALIDATE_INT) !== false) {
            
            $dealId = explode("_", $currState)[3];
            $url = $this->bitrixUrl.'/local/api/update_deal.php';
            $method = 'POST';
            $postData = json_encode([
                'deal_id' => $dealId,
                'count' => $message["message"]["text"]
            ], JSON_UNESCAPED_UNICODE);
            $ch = curl_init($url);
            
            $headers = [
                'Content-Type: application/json',
            ];
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POST => !empty($postData),
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]);
            $response = curl_exec($ch);
            $deal = json_decode($response, true);
            
            return $deal;
        } else {
            $this->sendRemovingHelpMessage($message, "Значение введено некорректно",5);
            return null;
        }
    }

    public function showDealDefaultMessage($message, $deal) {
        
        if (isset($message["callback_query"])) {
            $chatId = $message["callback_query"]["message"]["chat"]["id"];
        } else {
            $chatId = $message["message"]["chat"]["id"];
        }        
        $response = "Название сделки: " . $deal["TITLE"] . "\n";
        $response .= "Количество товара: " . $deal["UF_COUNT_PRODUCT"] . "\n";
        $response .= "Цена товара: " . $deal["UF_PRICE_PRODUCT"] . "\n";
        $response .= "Общая сумма: " . $deal["OPPORTUNITY"] . "\n";
        $keyboard[] = [
            [
                'text' => 'Изменить количество товара',
                'callback_data' => 'change_count_deal_' . $deal["ID"]
            ]
        ];
        $keyboard[] = [
            [
                'text' => 'Вернуться к сделкам',
                'callback_data' => 'back_to_deals'
            ]
        ];
    
        $inlineKeyboard = [
            'inline_keyboard' => $keyboard
        ];
        $encodedMarkup = json_encode($inlineKeyboard);
        
        $this->sendMessageKeyboard($chatId, $response, $encodedMarkup); 
    }

    public function processDealOptionCallback($message, $user) {
       
        // 
        if (isset($message["callback_query"]["data"])) {
            $chatId = $message["callback_query"]["message"]["chat"]["id"];
            $messageId = $message["callback_query"]["message"]["message_id"];
            if ($message["callback_query"]["data"] == "back_to_deals") {
                
                $this->deleteMessage($messageId ,$chatId);
                $this->getDeals($user->getBitrixId(), $message);
                
                return null;
            } else if (str_contains($message["callback_query"]["data"] , "change_count_deal")) {
                file_put_contents('./logs.log', print_r($message, true), FILE_APPEND);
                $this->deleteMessage($messageId ,$chatId);
                $response = "Пожалуйста введите целое значение для изменения количества товара";
                $this->sendMessage($chatId, $response);
                return $message["callback_query"]["data"];
            } else {
                
                $callbackQuery = explode("_", $message["callback_query"]["data"]);
                $dealId = $callbackQuery[1];
                // file_put_contents('./logs.log', print_r($dealId, true), FILE_APPEND);
                if ($dealId) {
                    $url = $this->bitrixUrl.'/local/api/get_deal.php';
                    $method = 'POST';
                    $postData = json_encode([
                        'deal_id' => $dealId,
                    ], JSON_UNESCAPED_UNICODE);
                    $ch = curl_init($url);
                    
                    $headers = [
                        'Content-Type: application/json',
                    ];
                    curl_setopt_array($ch, [
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_CUSTOMREQUEST => $method,
                        CURLOPT_HTTPHEADER => $headers,
                        CURLOPT_POST => !empty($postData),
                        CURLOPT_POSTFIELDS => $postData,
                        CURLOPT_TIMEOUT => 10,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                    ]);
                    $response = curl_exec($ch);
                    $deal = json_decode($response, true);
                    $this->showDealDefaultMessage($message, $deal);
                    $this->deleteMessage($messageId ,$chatId);
                    return null;
                }
            }

        }
        return null;
    }

    public function getDeals($bitrixId, $message) {
        if (isset($message["callback_query"])) {
            $chatId = $message["callback_query"]["message"]["chat"]["id"];
        } else {
            $chatId = $message["message"]["chat"]["id"];
        }
        
        $url = $this->bitrixUrl.'/local/api/get_deals.php';
        $method = 'POST';
        $postData = json_encode([
            'bitrix_id' => $bitrixId,
        ], JSON_UNESCAPED_UNICODE);
        $ch = curl_init($url);
        
        $headers = [
            'Content-Type: application/json',
        ];
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => !empty($postData),
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $response = curl_exec($ch);
        $deals = json_decode($response, true);
       
        $keyboard = [];
        
        foreach ($deals["deals"] as $deal) {
            // Обрезаем название, если слишком длинное
            $title = mb_strlen($deal['TITLE']) > 30 
                ? mb_substr($deal['TITLE'], 0, 30) . '...' 
                : $deal['TITLE'];
            
            $keyboard[] =[[
                    'text' => $title,
                    'callback_data' => 'deal_' . $deal['ID']
                ]];
        }
        $inlineKeyboard = [
            'inline_keyboard' => $keyboard
        ];
        $encodedMarkup = json_encode($inlineKeyboard);
        
        $this->sendMessageKeyboard($chatId, 'Пожалуйста, выберите сделку:', $encodedMarkup); 
    }

    private function sendRequest(string $url, array $params = []): array
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => !empty($params),
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        // file_put_contents('./logs.log', print_r($params, true), FILE_APPEND);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            
            return ['ok' => false, 'error' => $error];
        }

        return json_decode($response, true) ?? ['ok' => false];
    }
    
}