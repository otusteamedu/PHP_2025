<?php

namespace App\Core;

use AllowDynamicProperties;
use App\Core\Exceptions\NotFoundException;
use Exception;

#[AllowDynamicProperties]
class Kernel
{
    private Router $router;

    public function __construct(DIContainer $container)
    {
        $this->container = $container;
        $this->router = $container->make(Router::class);
    }

    public function handle()
    {
        try {
            $route = $this->router->getRoute();
            $controller = $route['controller'];
            $method = $route['method'];
            $params = $route['params'];

            return call_user_func_array([$controller, $method], $params);
        } catch (NotFoundException $e) {
            http_response_code(404);
            return '404 Not Found';
        } catch (Exception $e) {
            http_response_code(500);
            return $e->getMessage();
        }
    }
}
