<?php

namespace App;
use App\Controller\EmailValidatorController;
use App\Controller\StringController;
use App\Exception\IApplicationException;
use App\Http\Request;
use App\Http\Response;

class App
{
    private static array $routes = [
        '/' => [
            'METHOD' => 'POST',
            'CONTROLLER' => StringController::class,
        ],
        '/emails/validate' => [
            'METHOD' => 'POST',
            'CONTROLLER' => EmailValidatorController::class,
        ],
    ];
    public function run(): string
    {
        try {
            ob_start();
            $request = new Request();
            $method = $request->getMethod();
            $pathRequest = $request->getPath();

            foreach (self::$routes as $path => $route) {
                if (isset($route['METHOD']) && preg_match("#^{$path}$#", $pathRequest) && $method == $route['METHOD']) {
                    if (isset($route['CONTROLLER']) && class_exists($route['CONTROLLER'])) {
                        $objController = new $route['CONTROLLER']();
                        $response = $objController($request);
                    }
                    break;
                }
            }

            if (!isset($response)) {
                $response = new Response(['message' => 'Метод не найден'], 404);
            }
        } catch (IApplicationException $applicationException) {
            $response = new Response(['message'  => $applicationException->getMessage()], $applicationException->getHttpCode());
        }
        catch (\Throwable $e) {
            $response = new Response(['message' => 'Произошла ошибка'], 500);
        }

        http_response_code($response->getHttpCode());

        if (!empty($response->getHeaders())) {
            foreach ($response->getHeaders() as $header) {
                header($header);
            }
        }

        header('Content-Type: application/json');

        ob_end_flush();

        return json_encode($response->getResponse());
    }
}