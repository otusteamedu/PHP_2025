<?php

namespace Blarkinov\RabbitMq\Http;

class Response
{
    public function send(int $httpcode, mixed $data)
    {
        header('Content-type: application/json');
        echo json_encode($data);
        http_response_code($httpcode);
    }
}
