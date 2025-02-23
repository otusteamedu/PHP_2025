<?php

namespace app;

class Json
{
    /**
     * @param int $code
     * @param array $response
     * @return string
     */
    public static function getResponse(int $code, array $response): string
    {
        http_response_code($code);
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}