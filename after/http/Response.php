<?php

namespace App\http;

class Response {
    public static function send(array $validStrings): void {
        header('Content-Type: application/json');
        echo json_encode($validStrings);
    }

    public static function sendError(string $message, int $code = 400): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
        exit;
    }
}