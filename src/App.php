<?php

namespace App;

class App
{
    public static function run() {
        $request = new Request();
        $response = Router::dispatch($request);

        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $key => $value) {
            header("$key: $value");
        }

        echo $response->getBody();
    }
}