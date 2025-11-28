<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Service\EmailProcess;
use App\Http\JsonResponse;

try {
    //Обработка emails и получаю объект с результатами
    $emailResult = EmailProcess::processEmails(__DIR__ . '/../emails.txt');
    
    /**
     * JsonResponse отвечает за HTTP ответ в JSON
     * Создается ответ с уже обновленными данными
     */
    $response = new JsonResponse($emailResult->toArray());

    // Заголовки внутри метода send() и получается вывод JSON клиенту
    $response->send();
    
} catch (Exception $e) {

    //Вывод ошибки с описанием через JsonResponse
    $errorResponse = new JsonResponse(['error' => $e->getMessage()], 500);
    $errorResponse->send();
}
?>