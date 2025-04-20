<?php
namespace Core;

use Core\Router;

class Kernel
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    /**
     * @return string
     */
    public function handle(): string
    {
        $route = $this->router->getRoute();
        if (!$route) {
            return json_encode(['error' => 'Route not found']);
        }

        [$controllerInstance, $method, $params] = $route;

        $response = call_user_func_array([$controllerInstance, $method], $params);
        return json_encode($response);
    }
}
