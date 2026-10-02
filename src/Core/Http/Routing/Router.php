<?php

declare(strict_types=1);

namespace App\Core\Http\Routing;

use App\Core\Http\Controller\Factory\ControllerFactoryInterface;
use App\Core\Http\Exception\NotFoundException;
use App\Core\Http\Message\Response;
use App\Core\Utils\PathResolverInterface;

class Router
{
    private readonly array $routes;

    public function __construct(
        private readonly ControllerFactoryInterface $controllerFactory,
        PathResolverInterface $pathResolver,
    ) {
        $this->routes = require $pathResolver->getConfigPath() . '/routes.php';
    }

    public function dispatch(string $requestPath, string $requestMethod): Response
    {
        $routes = array_filter(
            $this->routes,
            static function(array $params) use ($requestMethod) {
                if (is_string($params['method'])) {
                    return $params['method'] === $requestMethod;
                } elseif(is_array($params['method'])) {
                    return in_array($requestMethod, $params['method'], true);
                } else {
                    return false;
                }
            },
        );

        foreach ($routes as $params) {
            if ($params['path'] === $requestPath) {
                $controllerClassName = $params['controller'];
                $methodName = $params['action'];
                return $this->controllerFactory->createHttpController($controllerClassName)->$methodName();
            }
        }

        throw new NotFoundException("Route not found for $requestMethod $requestPath");
    }
}
