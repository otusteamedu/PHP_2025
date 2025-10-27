<?php

namespace Blarkinov\PhpDbCourse\Controllers;

use Blarkinov\PhpDbCourse\Http\Response;

class MainController
{
    private Response $response;

    public function __construct()
    {
        $this->response = new Response();
    }

    public function notFound()
    {
        $this->response->send(404, ['error' => 'not found']);
    }

    public function badRequest(string $message='bad request')
    {
        $this->response->send(400, ['error' => $message]);
    }
}
