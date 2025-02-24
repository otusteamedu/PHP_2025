<?php

namespace App;

use App\Http\Request;
use App\Http\Response;
use App\Controllers\HomeController;

class Router
{
    public static function dispatch(Request $request): Response
    {
        $uri = trim($request->getUri(), '/');
        $method = $request->getMethod();
        $controller = new HomeController();

        if ($uri === 'test' && $method === 'GET') {
            return $controller->viewTest();
        }

        if ($uri === 'check' && $method === 'POST') {
            return $controller->checkStr($request);
        }

        if ($uri === 'check-emails' && $method === 'POST') {
            return $controller->checkEmails($request);
        }

        return new Response(404, ['Content-Type' => 'application/json'], json_encode(['error' => 'Not Found']));
    }
}
