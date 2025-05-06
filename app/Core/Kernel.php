<?php

declare(strict_types=1);

namespace App\Core;

use AllowDynamicProperties;
use App\Core\Exceptions\NotFoundException;
use Exception;
use ReflectionException;

#[AllowDynamicProperties]
class Kernel
{
    private Router $router;

    /**
     * @throws ReflectionException
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->router = $container->make(Router::class);
    }

    /**
     * @return mixed|string
     */
    public function handle(): mixed
    {
        try {
            $route = $this->router->getRoute();
            $controller = $route['controller'];
            $method = $route['method'];
            $params = $route['params'];

            return call_user_func_array([$controller, $method], $params);
        } catch (NotFoundException) {
            http_response_code(404);
            return '404 Not Found';
        } catch (Exception $e) {
            http_response_code(500);
            return $e->getMessage();
        }
    }
}