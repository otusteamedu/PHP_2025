<?php
require __DIR__ . '/vendor/autoload.php';

use Ak\Hw\Services\EmailFinder;

/**
 * Отправляет JSON-ответ клиенту и завершает выполнение скрипта.
 *
 * @param bool $isSuccess Успешен ли был запрос.
 * @param array $data Данные для включения в ответ.
 * @param int $statusCode HTTP-код ответа.
 * @throws JsonException
 */
function sendJsonResponse(bool $isSuccess, array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    $response = ['success' => $isSuccess];
    if ($isSuccess) {
        $response['data'] = $data;
    } else {
        $response['error'] = $data;
    }
    echo json_encode($response, JSON_THROW_ON_ERROR);
    exit();
}


$text = htmlspecialchars($_REQUEST['text'] ?? '');

if (empty($text)) {
    sendJsonResponse(false, ['message' => 'Text parameter is required.'], 400);
}

try {
    $validEmails = new EmailFinder()->getEmailsByText($text);
    sendJsonResponse(true, ['emails' => $validEmails]);
} catch (Exception $e) {
    sendJsonResponse(false, ['message' => $e->getMessage()], $e->getCode());
}
