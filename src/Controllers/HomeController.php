<?php

namespace App\Controllers;

use App\Models\Validator;

use App\Http\Request;
use App\Http\Response;

class HomeController
{
    public function checkEmails(Request $request): Response
    {
        $data = $request->getBody();

        try {
            // Проверка email с помощью модели Validator
            $result = Validator::checkEmails($data['emails']);

            // Возвращаем список с корректностью email
            return new Response(200, ['Content-Type' => 'application/json'], json_encode(['status'=>'success', 'emails' => $result]));
        } catch (\Exception $e) {
            // Если пустой массив
            return new Response(200, ['Content-Type' => 'application/json'], json_encode(['status'=>'fail', 'message' => 'Error: '.$e->getMessage()]));
        }
    }

    public function checkStr(Request $request): Response
    {
        $data = $request->getBody();

        try {
            // Проверка строки с помощью модели Validator
            Validator::validateString($data['string']);

            // Если строка корректна, возвращаем успешный результат
            return new Response(200, ['Content-Type' => 'application/json'], json_encode(['message' => 'All right']));
        } catch (\Exception $e) {
            // Если строка некорректна, возвращаем ошибку
            return new Response(200, ['Content-Type' => 'application/json'], json_encode(['message' => 'Error: '.$e->getMessage()]));
        }
    }

    public function viewTest() {
        $message = "Request Chain: " . ($_SERVER['HTTP_X_REQUEST_CHAIN'] ?? 'Unknown') . gethostname();
        return new Response(200, ['Content-Type' => 'text/plain'], json_encode(['message' => $message]));
    }

}
