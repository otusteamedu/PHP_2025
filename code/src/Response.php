<?php

namespace App;

class Response {
    public function ok($message) {
        http_response_code(200);
        return $message;
    }

    public function badRequest($message) {
        http_response_code(400);
        return $message;
    }
}
