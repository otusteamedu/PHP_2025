<?php

declare(strict_types=1);

namespace App\Controller;

use App\Validator\ParenthesisStringValidator;

class ParenthesisStringController
{
    public function verifyParenthesisString(): void
    {
        try {
            $parenthesisStr = $this->getPayload();
            $isValidStr = ParenthesisStringValidator::isValid($parenthesisStr);
            $httpCode = $isValidStr ? 200 : 400;
            $message = $isValidStr ? 'Всё хорошо' : 'Всё плохо';
        } catch (\Exception $e) {
            $httpCode = $e->getCode();
            $message = $e->getMessage();
        }

        header('Content-Type: application/json; charset=utf-8', response_code: $httpCode);
        echo json_encode(['response' => $message]);
    }

    /**
     * @throws \Exception
     */
    private function getPayload(): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new \Exception('Method Not Allowed (Allow: POST)', 405);
        }

        $body = file_get_contents('php://input');
        if ($body === false) {
            throw new \Exception('Не удалось получить данные из тела запроса', 400);
        }
        try {
            $body = json_decode($body, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \Exception($e->getMessage(), 400);
        }

        if (!isset($body['string'])) {
            throw new \Exception('Параметр "string" не передан в теле запроса', 400);
        }

        return $body['string'];
    }
}
