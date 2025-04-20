<?php

namespace Core;

class Kernel
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    /**
     * @return string|bool
     */
    public function handle(): string|bool
    {
        [
            'controller' => $controller,
            'method' => $method,
            'methodParams' => $methodParams
        ] = $this->router->getRoute();

        $result = $controller->$method(...$methodParams);
        return json_encode($result);
    }
}