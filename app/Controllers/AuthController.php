<?php

namespace App\Controllers;

use App\Services\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    public function __construct(private UserService $userService) {}

    public function register(Request $request, Response $response, $args) 
    {
        $parsedBody = $request->getParsedBody();

        $email = filter_var($parsedBody['email'], FILTER_VALIDATE_EMAIL);
        if ($email === false) {
            $payload = json_encode(['message' => 'Невалидный email']);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $password = htmlspecialchars($parsedBody['password']);
        if (strlen($password) < 3) {
            $payload = json_encode(['message' => 'Пароль должен быть не менее 3 символов']);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $jwt = $this->userService->registerUser($email, $password);

        $payload = json_encode(['jwt' => $jwt]);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}