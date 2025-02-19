<?php

namespace Src;

use Src\Validator;
use Src\ExceptionHandler;

class App {
    public function run(): string {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return json_encode(["error" => "405 Method Not Allowed: Используйте POST."]);
        }

        try {
            $inputString = $_POST['string'] ?? '';

            if (empty($inputString)) {
                throw new \Exception("Строка пуста.");
            }

            if (!Validator::isValidBrackets($inputString)) {
                throw new \Exception("Скобки некорректны.");
            }

            http_response_code(200);
            return json_encode(["message" => "Скобки корректны."]);
        } catch (\Exception $e) {
            return ExceptionHandler::handle($e);
        }
    }
}

