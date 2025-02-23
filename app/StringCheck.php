<?php

namespace app;

class StringCheck
{
    /**
     * @return string
     */
    public function check(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $string = $_POST['string'] ?? '';

            // Проверка на непустоту
            if (empty($string)) {
                return Json::getResponse(400, ["error" => "Строка пуста!"]);
            }

            $response = Verification::verificate($string);
            if ($response) {
                return Json::getResponse(200, ["message" => "Скобки корректны!"]);
            }

            return Json::getResponse(400, ["error" => "Скобки некорректны!"]);
        }
        return Json::getResponse(405 , ["error" => "Отправьте запрос методом POST!"]);
    }
}
