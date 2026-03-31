<?php

declare(strict_types=1);

namespace AppV2;

use Throwable;

class App
{
    public function handleRequest(): void
    {
        try {
            $requestBody = new RequestBodyService();
            $service = new EmailChecker(); //лучше добавить конструктор для будущего покрытия тестами

            $decodedRequestBody = $requestBody->getDecodedJsonBody();
            $emailList = $decodedRequestBody['emailList'];

            $isValidEmailList = $service->isValidEmailList($emailList); //при обработке запроса напрямую обращаемся к сервису, лучше это делать из хендлера. Данный класс должен работать только с получением запроса и ответом на него, тк в будущем возможно расширение

            $isValidEmailList
                ? $this->handleResponse('Email list is valid')
                : $this->handleResponse('Email list is not valid', 400);
        } catch (Throwable) {
            $this->handleResponse('Invalid request body', 400);
        }
    }

    private function handleResponse(string $message, int $statusCode = 200): void {
        http_response_code($statusCode);
        echo $message;
    }
}
