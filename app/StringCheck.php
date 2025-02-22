<?php

namespace app;

class StringCheck
{
    public function check(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $string = $_POST['string'] ?? '';

            // Проверка на непустоту
            if (empty($string)) {
                Json::getResponse(400, ["error" => "Строка пуста!"]);
            }

            $response = Verification::verificate($string);
            if ($response) {
                Json::getResponse(200, ["message" => "Скобки корректны!"]);
            }

            Json::getResponse(400, ["error" => "Скобки некорректны!"]);
        }
        Json::getResponse(405 , ["error" => "Отправьте запрос методом POST!"]);
    }
}
