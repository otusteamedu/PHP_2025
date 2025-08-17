<?php

namespace App;

use App\Auth;
use App\Exceptions\EmptyStringException;
use App\Validator;
use App\Response;


class App {
    private Auth $auth;
    private Validator $validator;
    private Response $response;

    public function __construct() {
        $this->auth = new Auth();
        $this->validator = new Validator();
        $this->response = new Response();
    }

    public function run(): string {
        try {
            // Инициализация сессии
            $this->auth->initSession();

            // Проверка метода запроса
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return $this->response->createResponse(405, 'Method Not Allowed');
            }

            // Получение и валидация строки
            $string = $_POST['string'];

            if (empty($string)) {
                throw new EmptyStringException("String is empty");
            }

            $result = $this->validator->isValidBracketString($string);

            // Формирование ответа
            $statusCode = $result['is_valid'] ? 200 : 400;
            return $this->response->createResponse($statusCode, $result['message']);

        } catch (\Exception $e) {
            return $this->response->createResponse(400, 'Bad request: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
        }
    }
}