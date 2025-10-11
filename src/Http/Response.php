<?php

namespace Blarkinov\RedisCourse\Http;

class Response
{
    public function send(int $httpcode, array $data)
    {
        header('Content-type: application/json');
        echo json_encode($data);
        http_response_code($httpcode);
    }
}
