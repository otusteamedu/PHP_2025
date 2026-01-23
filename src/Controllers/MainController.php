<?php

namespace Blarkinov\RedisCourse\Controllers;

use Blarkinov\RedisCourse\Http\Response;

class MainController
{
    private Response $response;

    public function __construct()
    {
        $this->response = new Response();
    }

    public function notFound()
    {
        $this->response->send(404, ['message' => 'not found']);
    }

    public function badRequest()
    {
        $this->response->send(400, ['message' => 'bad request']);
    }
}
