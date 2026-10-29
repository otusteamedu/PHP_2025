<?php

declare(strict_types=1);

namespace App\Core\Http\Routing;

use App\Core\Http\Controller\Factory\ControllerFactoryInterface;
use App\Core\Http\Exception\InvalidRouteConfigException;
use App\Core\Http\Exception\NotFoundException;
use App\Core\Http\Message\Response;
use App\Core\Utils\PathResolverInterface;

class Router
{
    private const array ROUTE_FIELDS = ['method', 'path', 'controller', 'action'];

    /**
     * @var list<array{
     *     method: string|list<string>,
     *     path: string,
     *     controller: class-string,
     *     action: string
     * }>
     */
    private readonly array $routes;
    private ?InvalidRouteConfigException $configError = null;

    public function __construct(
        private readonly ControllerFactoryInterface $controllerFactory,
        PathResolverInterface $pathResolver,
    ) {
        try {
            $this->routes = $this->loadRoutes($pathResolver);
        } catch (InvalidRouteConfigException $e) {
            error_log('[ROUTER CONFIG] ' . $e->getMessage());
            $this->configError = $e;
            $this->routes = [];
        }
    }

    /**
     * @throws InvalidRouteConfigException
     * @throws NotFoundException
     */
    public function dispatch(string $requestPath, string $requestMethod): Response
    {
        if ($this->configError !== null) {
            throw $this->configError;
        }

        foreach ($this->routes as $route) {
            if ($route['path'] === $requestPath && $this->methodMatches($route['method'], $requestMethod)) {
                $controller = $this->controllerFactory->createHttpController($route['controller']);

                return $controller->{$route['action']}();
            }
        }

        throw new NotFoundException("Route not found for $requestMethod $requestPath");
    }

    /**
     * @throws InvalidRouteConfigException
     */
    private function loadRoutes(PathResolverInterface $pathResolver): array
    {
        $routes = require $pathResolver->getConfigPath() . '/routes.php';

        if (!is_array($routes)) {
            throw new InvalidRouteConfigException('Route config file must return an array.');
        }

        $validated = [];
        foreach ($routes as $index => $route) {
            $validated[] = $this->validateRoute($route, $index);
        }

        return $validated;
    }

    private function methodMatches(string|array $allowed, string $requestMethod): bool
    {
        return match (true) {
            is_string($allowed) => $allowed === $requestMethod,
            default => in_array($requestMethod, $allowed, true),
        };
    }

    /**
     * @throws InvalidRouteConfigException
     */
    private function validateRoute(mixed $route, int $index): array
    {
        $location = "route #$index";

        if (!is_array($route)) {
            throw new InvalidRouteConfigException("$location: route must be an array.");
        }

        foreach (self::ROUTE_FIELDS as $field) {
            if (!array_key_exists($field, $route)) {
                throw new InvalidRouteConfigException("$location: missing key '$field'.");
            }
        }

        if (!is_string($route['path'])) {
            throw new InvalidRouteConfigException("$location: 'path' must be a string.");
        }

        if (!self::isAllowedMethodFormat($route['method'])) {
            throw new InvalidRouteConfigException(
                "$location: 'method' must be a string or a list of strings.",
            );
        }

        if (!self::isExistingControllerAction($route['controller'], $route['action'])) {
            throw new InvalidRouteConfigException(
                "$location: controller/action '{$route['controller']}::{$route['action']}' does not exist.",
            );
        }

        return $route;
    }

    private static function isAllowedMethodFormat(mixed $method): bool
    {
        if (is_string($method)) {
            return true;
        }

        return is_array($method)
            && array_is_list($method)
            && !in_array(false, array_map(is_string(...), $method), true);
    }

    private static function isExistingControllerAction(string $controller, mixed $action): bool
    {
        return class_exists($controller)
            && is_string($action)
            && method_exists($controller, $action);
    }
}
