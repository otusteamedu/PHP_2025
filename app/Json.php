<?php

namespace app;

class Json
{
    public static function getResponse(int $code, array $response)
    {
        http_response_code($code);
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}