<?php

namespace App\Controllers;

use App\Models\Validator;

class HomeController
{
    public function checkStr()
    {
        $string = $_POST['string'] ?? '';

        try {
            // Проверка строки с помощью модели Validator
            Validator::validateString($string);

            // Если строка корректна, возвращаем успешный результат
            $response = [
                'status' => 200,
                'message' => 'Все хорошо'
            ];
        } catch (\Exception $e) {
            // Если строка некорректна, возвращаем ошибку
            $response = [
                'status' => 400,
                'message' => 'Все плохо: ' . $e->getMessage()
            ];
        }

        return $response;
    }

    public function viewTest() {
            return "Request Chain: " . ($_SERVER['HTTP_X_REQUEST_CHAIN'] ?? 'Unknown') . gethostname(). "\n";
        }

}
