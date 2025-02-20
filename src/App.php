<?php

namespace Hafiz\Php2025;

class App {
    public function run(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Response::json(["error" => "405 Method Not Allowed: Используйте POST."], 405);
        }

        try {
            $inputString = $_POST['string'] ?? '';

            if (empty($inputString)) {
                throw new \Exception("Строка пуста.");
            }

            if (!Validator::isValidBrackets($inputString)) {
                throw new \Exception("Скобки некорректны.");
            }

            Response::json(["message" => "Скобки корректны."]);
        } catch (\Exception $e) {
            Response::json(["error" => $e->getMessage()], 400);
        }
    }
}
