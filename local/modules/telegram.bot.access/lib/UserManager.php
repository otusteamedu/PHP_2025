<?php
namespace Telegram\Bot\Access;

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main\Config\Option;
// CModule::IncludeModule('highloadblock');
class UserManager
{
    private $hlBlockId;
    private $entity;
    private $apiUrl;
    private $apiToken;

    public function __construct()
    {
        Loader::includeModule('highloadblock');
        
        // Получаем ID HL-блока
        $this->hlBlockId = $this->getHlBlockId();
        if ($this->hlBlockId) {
            $this->entity = $this->getEntity();
        }
        
        // Настройки API из настроек модуля
        $this->apiUrl = Option::get('telegram.bot.access', 'api_url', '');
        $this->apiToken = Option::get('telegram.bot.access', 'api_token', '');
    }

    /**
     * Добавить пользователя в разрешенные
     */
    public function addUser($userId, $userFio, $telegramLogin)
    {
        if (!$this->entity) {
            return ['success' => false, 'error' => 'HL-блок не найден'];
        }
        
        // Проверяем, существует ли уже пользователь
        $existing = $this->getUserByBitrixId($userId);
        if ($existing) {
            return ['success' => false, 'error' => 'Пользователь уже добавлен'];
        }
        
        try {
            // Добавляем в HL-блок
            $data = [
                'UF_USER_ID' => $userId,
                'UF_USER_FIO' => $userFio,
                'UF_TELEGRAM_LOGIN' => $telegramLogin,
                'UF_DATE_CREATE' => new \Bitrix\Main\Type\DateTime()
            ];
            
            $result = $this->entity::add($data);
            
            if ($result->isSuccess()) {
                $recordId = $result->getId();
                
                // Отправляем данные в API
                $apiResult = $this->sendToApi('add', [
                    'record_id' => $recordId,
                    'bitrix_id' => $userId,
                    'fio' => $userFio,
                    'username' => $telegramLogin
                ]);
                if ($apiResult["success"]) {
                    // $logDir = $_SERVER['DOCUMENT_ROOT'] . '/upload/telegram_bot_api_logs/';                    
                    // $logFile = $logDir . date('Y-m-d') . '.log';
                    // file_put_contents($logFile, print_r($apiResult, true) . "\n\n", FILE_APPEND);
                    if ($apiResult["response"]["success"])  {
                        return [
                            'success' => true,                            
                        ];
                    } else {
                        $this->entity::delete($recordId);
                        return ['success' => false, 'error' => "Ответ API:" . $apiResult["response"]["error"]];
                    }
                   
                } else {
                    $this->entity::delete($recordId);
                    return ['success' => false, 'error' => "API не доступен: " . $apiResult["error"]];
                }
                
            } else {
                $this->entity::delete($recordId);
                return ['success' => false, 'error' => $result->getErrorMessages()];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Удалить пользователя из разрешенных
     */
    public function deleteUser($recordId)
    {
        if (!$this->entity) {
            return ['success' => false, 'error' => 'HL-блок не найден'];
        }
        
        try {
            // Получаем данные перед удалением для отправки в API
            $record = $this->entity::getById($recordId)->fetch();
            
            if (!$record) {
                return ['success' => false, 'error' => 'Запись не найдена'];
            }
            
            // Удаляем из HL-блока
            $result = $this->entity::delete($recordId);
            
            if ($result->isSuccess()) {
                // Отправляем данные в API
                $apiResult = $this->sendToApi('delete', [
                    'record_id' => $recordId,
                    'bitrix_id' => $record['UF_USER_ID'],
                    'fio' => $record['UF_USER_FIO'],
                    'username' => $record['UF_TELEGRAM_LOGIN']
                ]);
                
                return [
                    'success' => true,
                    'api_response' => $apiResult
                ];
            } else {
                return ['success' => false, 'error' => $result->getErrorMessages()];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Получить всех пользователей
     */
    public function getAllUsers()
    {
        if (!$this->entity) {
            return [];
        }
        
        $result = $this->entity::getList([
            'select' => ['*'],
            'order' => ['ID' => 'DESC']
        ]);
        
        return $result->fetchAll();
    }

    /**
     * Получить пользователя по ID Bitrix
     */
    public function getUserByBitrixId($userId)
    {
        if (!$this->entity) {
            return null;
        }
        
        $result = $this->entity::getList([
            'select' => ['*'],
            'filter' => ['UF_USER_ID' => $userId],
            'limit' => 1
        ]);
        
        return $result->fetch();
    }

    // /**
    //  * Отправка данных во внешнее API
    //  */
    // private function sendToApi($action, $data)
    // {
    //     if (empty($this->apiUrl)) {
    //         return ['success' => false, 'error' => 'API URL не настроен'];
    //     }
        
    //     try {
    //         $httpClient = new \Bitrix\Main\Web\HttpClient();
    //         $httpClient->setHeader('Content-Type', 'application/json');
            
    //         // if (!empty($this->apiToken)) {
    //         //     $httpClient->setHeader('Authorization', 'Bearer ' . $this->apiToken);
    //         // }
            
    //         $payload = [
    //             'action' => $action,
    //             'data' => $data,
    //             'timestamp' => time()
    //         ];
            
    //         $response = $httpClient->post($this->apiUrl, json_encode($payload));
    //         $decodedResponse = json_decode($response, true);
            
    //         return [
    //             'success' => $httpClient->getStatus() == 200,
    //             'status' => $httpClient->getStatus(),
    //             'response' => $decodedResponse ?: $response
    //         ];
    //     } catch (\Exception $e) {
    //         return ['success' => false, 'error' => $e->getMessage()];
    //     }
    // }

     /**
     * Отправка данных на внешнее API
     */
    private function sendToApi($action, $data)
    {
        if (empty($this->apiUrl)) {
            return ['success' => false, 'error' => 'Базовый URL API не настроен'];
        }
        
        // Формируем полный URL в зависимости от действия
        $url = $this->apiUrl;
        // $method = 'POST';
        $postData = null;
        
        switch ($action) {
            case 'add':
                // Добавление: POST запрос на /api/users/
                $url = rtrim($this->apiUrl, '/') . '/api/users';
                $method = 'POST';
                $postData = json_encode([
                    'bitrix_id' => $data['bitrix_id'],
                    'fio' => $data['fio'],
                    'username' => $data['username']
                ], JSON_UNESCAPED_UNICODE);
                break;
                
            case 'update':
                // Изменение: PUT запрос на /api/users/bitrix/{id}
                $url = rtrim($this->apiUrl, '/') . '/api/users/bitrix/' . $data['user_id'];
                $method = 'PUT';
                $postData = json_encode([
                    'bitrix_id' => $data['bitrix_id'],
                    'fio' => $data['fio'],
                    'username' => $data['username']
                ], JSON_UNESCAPED_UNICODE);
                break;
                
            case 'delete':
                // Удаление: DELETE запрос на /api/users/bitrix/{id}
                $url = rtrim($this->apiUrl, '/') . '/api/users/bitrix/' . $data['bitrix_id'];
                $method = 'DELETE';
                break;
                
            default:
                return ['success' => false, 'error' => 'Неизвестное действие: ' . $action];
        }
        
        // Настройка curl
        $ch = curl_init($url);
        
        $headers = [
            'Content-Type: application/json',
        ];
        
        // Добавляем токен авторизации, если он настроен
        if (!empty($this->apiToken)) {
            $headers[] = 'Authorization: Bearer ' . $this->apiToken;
        }
        
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        
        // Добавляем данные для PUT и POST запросов
        if (in_array($method, ['PUT', 'POST']) && !empty($postData)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        
        // Для отладки можно включить вывод заголовков
        curl_setopt($ch, CURLOPT_HEADER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        // Логирование запросов (для отладки)
        $this->logApiRequest($action, $url, $method, $postData, $response, $httpCode, $error);
        
        if ($error) {
            return [
                'success' => false,
                'error' => 'CURL ошибка: ' . $error,
                'http_code' => $httpCode,
                'action' => $action
            ];
        }
        
        // Проверяем успешные коды ответа
        $successCodes = [200, 201, 204];
        $isSuccess = in_array($httpCode, $successCodes);
        
        return [
            'success' => $isSuccess,
            'http_code' => $httpCode,
            'response' => json_decode($response, true) ?: $response,
            'action' => $action,
            'url' => $url
        ];
    }

    private function logApiRequest($action, $url, $method, $requestData, $response, $httpCode, $error)
    {
        // Можно сохранять в лог-файл или в отдельную таблицу БД
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'action' => $action,
            'url' => $url,
            'method' => $method,
            'request' => $requestData,
            'response' => $response,
            'http_code' => $httpCode,
            'error' => $error
        ];
        
        // Пример записи в файл (раскомментировать при необходимости)
        
        $logDir = $_SERVER['DOCUMENT_ROOT'] . '/upload/telegram_bot_api_logs/';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . date('Y-m-d') . '.log';
        file_put_contents($logFile, print_r($logData, true) . "\n\n", FILE_APPEND);
        
    }

    /**
     * Получить ID HL-блока
     */
    private function getHlBlockId()
    {
        $hlBlock = HL\HighloadBlockTable::getList([
            'filter' => ['=NAME' => 'TelegramBotAccess']
        ])->fetch();
        
        return $hlBlock ? $hlBlock['ID'] : null;
    }

    /**
     * Получить сущность HL-блока
     */
    private function getEntity()
    {
        if (!$this->hlBlockId) {
            return null;
        }
        
        $hlBlock = HL\HighloadBlockTable::getById($this->hlBlockId)->fetch();
        if (!$hlBlock) {
            return null;
        }
        
        $entity = HL\HighloadBlockTable::compileEntity($hlBlock);
        return $entity->getDataClass();
    }

    /**
     * Обновить настройки API
     */
    public function updateApiSettings($apiUrl, $apiToken)
    {
        Option::set('telegram.bot.access', 'api_url', $apiUrl);
        Option::set('telegram.bot.access', 'api_token', $apiToken);
        
        $this->apiUrl = $apiUrl;
        $this->apiToken = $apiToken;
    }
}
?>