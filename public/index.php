<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Service\EmailProcess;
use App\Http\JsonResponse;

try {
    // Обработка emails и получаю объект с результатами
    $emailResult = EmailProcess::processEmails(__DIR__ . '/../emails.txt');
    
    /**
     * JsonResponse отвечает за HTTP ответ в JSON
     * Создается ответ с уже обновленными данными
     */
    $response = new JsonResponse($emailResult->toArray());
	
	$response->sendHeaders();
    echo $response->getContent();
    
} catch (AppException $e) {
    $response = $e->toResponse();
	
	 $response->sendHeaders();
    echo $response->getContent();
	
} catch (Exception $e) {
    $response = new JsonResponse(['error'=>'Ошибка сервера'], 500);
	
	$response->sendHeaders();
    echo $response->getContent();
}

?>