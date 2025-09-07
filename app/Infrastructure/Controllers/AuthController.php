<?php

namespace App\Infrastructure\Controllers;

use App\Helpers\HttpHelper;
use App\Application\Services\UserService;
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
            return HttpHelper::send400Error('Невалидный email', $response);
        }

        $password = htmlspecialchars($parsedBody['password']);
        if (strlen($password) < 3) {
            return HttpHelper::send400Error('Пароль должен быть не менее 3 символов', $response);
        }

        $jwt = $this->userService->registerUser($email, $password);

        return HttpHelper::sendData(['jwt' => $jwt], $response);
    }

    public function login(Request $request, Response $response, $args)
    {
        $parsedBody = $request->getParsedBody();

        $email = filter_var($parsedBody['email'], FILTER_VALIDATE_EMAIL);
        if ($email === false) {
            return HttpHelper::send400Error('Невалидный email', $response);
        }

        $password = htmlspecialchars($parsedBody['password']);
        if (strlen($password) < 3) {
            return HttpHelper::send400Error('Пароль должен быть не менее 3 символов', $response);
        }

        $jwt = $this->userService->loginUser($email, $password);
        if ($jwt === '') {
            return HttpHelper::send400Error('Неверные email/пароль', $response);
        }

        return HttpHelper::sendData(['jwt' => $jwt], $response);
    }
}