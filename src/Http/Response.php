<?php

namespace Blarkinov\PhpDbCourse\Http;

class Response
{
    public function send(int $httpcode, array|object|null $data)
    {
        header('Content-type: application/json');
        if($data) echo json_encode($data);
        http_response_code($httpcode);
        if($httpcode===200)
            exit(0);
        else
            exit(1);
    }
}
