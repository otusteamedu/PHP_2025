<?php

namespace App\Helpers;

class HttpHelper
{
    public static function send400Error($message, $response)
    {
        $payload = json_encode(['message' => $message]);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    public static function send401Error($response)
    {
        $payload = json_encode(['message' => 'Пользователь не авторизован']);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }

    public static function send404Error($response)
    {
        $payload = json_encode(['message' => 'Не найдено']);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    public static function sendData($data, $response)
    {
        $payload = json_encode($data);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}