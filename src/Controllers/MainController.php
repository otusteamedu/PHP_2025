<?php

namespace Blarkinov\ElasticApp\Controllers;

use Blarkinov\ElasticApp\CLI\Response;

class MainController
{
    private Response $response;

    public function __construct()
    {
        $this->response = new Response();
    }

    public function notFound()
    {
        $this->response->send(1, 'Error: not found command');
    }

    public function badRequest(string $message = 'Error: bad command')
    {
        $this->response->send(1, "Error: $message");
    }
}
