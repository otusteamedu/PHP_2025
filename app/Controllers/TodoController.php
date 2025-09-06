<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TodoController 
{
    public function index (Request $request, Response $response, $args) 
    {
        $data = [];
        $data[] = ['id' => 1, 'user_id' => 1, 'content' => 'todo1', 'created_at' => '2025-01-01 20:00'];
        $data[] = ['id' => 2, 'user_id' => 1, 'content' => 'todo2', 'created_at' => '2025-01-01 21:00'];

        $payload = json_encode($data);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}