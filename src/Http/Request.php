<?php

namespace Blarkinov\RedisCourse\Http;

use Blarkinov\RedisCourse\Router;
use Blarkinov\RedisCourse\Controllers\MainController;
use Blarkinov\RedisCourse\Service\Validator;
use Throwable;

class Request
{

    public function __construct() {}

    public function handle(array $routes)
    {

        $router = new Router();

        foreach ($routes as $route) {
            $router->add($route['method'], $route['pattern'], $route['handler']);
        }

        $mainController = new MainController();

        if (!(new Validator)->mainValidate()) {
            $mainController->badRequest();
            return;
        }

        try {
            $router->dispatch(
                $_SERVER['REQUEST_METHOD'],
                parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
            );
            
        } catch (\Throwable $th) {
            $mainController->badRequest($th->getMessage());
        }
    }
}
