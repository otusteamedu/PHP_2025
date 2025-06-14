<?php

namespace App\Core;

use App\Core\Exceptions\NotFoundException;
use Closure;
use InvalidArgumentException;
use ReflectionException;
use ReflectionMethod;
use ReflectionType;

class Router
{
    private array $routes;
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->loadRoutes();
    }

    protected function loadRoutes(): void
    {
        $routesPath = __DIR__ . '/../../app/routes.php';
        if (!file_exists($routesPath)) {
            throw new \RuntimeException("Routes file not found at: {$routesPath}");
        }

        $this->routes = include $routesPath;
    }

    /**
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function getRoute(): array
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route => $routeData) {
            [$routeMethod, $handler] = $routeData;
            $pattern = $this->convertRouteToPattern($route);

            if ($method === $routeMethod && preg_match('#^' . $pattern . '$#', $requestUri, $matches)) {
                $params = array_slice($matches, 1);
                return $this->resolveHandler($handler, $params);
            }
        }

        throw new NotFoundException("Route not found");
    }

    protected function convertRouteToPattern(string $route): string
    {
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route);
        return str_replace('/', '\/', $pattern);
    }

    /**
     * @throws ReflectionException
     */
    protected function resolveHandler(array|Closure $handler, array $params): array
    {
        if ($handler instanceof Closure) {
            return [
                'controller' => $this,
                'method' => '__invoke',
                'params' => $params
            ];
        }

        [$controllerClass, $methodName] = $handler;
        $controller = $this->container->make($controllerClass);

        return [
            'controller' => $controller,
            'method' => $methodName,
            'params' => $this->resolveMethodParams($controller, $methodName, $params)
        ];
    }

    /**
     * @throws ReflectionException
     */
    protected function resolveMethodParams(object $controller, string $method, array $routeParams): array
    {
        $reflection = new ReflectionMethod($controller, $method);
        $resolvedParams = [];

        foreach ($reflection->getParameters() as $param) {
            $name = $param->getName();
            $type = $param->getType();

            if (array_key_exists($name, $routeParams)) {
                $resolvedParams[] = $this->castParameter($routeParams[$name], $type);
            } elseif ($type && !$type->isBuiltin()) {
                $resolvedParams[] = $this->container->make($type->getName());
            } elseif ($param->isDefaultValueAvailable()) {
                $resolvedParams[] = $param->getDefaultValue();
            } else {
                throw new InvalidArgumentException("Missing required parameter: {$name}");
            }
        }

        return $resolvedParams;
    }

    /**
     * @param $value
     * @param ReflectionType|null $type
     * @return float|int|mixed|string
     */
    protected function castParameter($value, ?ReflectionType $type): mixed
    {
        if (!$type) {
            return $value;
        }

        $typeName = $type->getName();

        switch ($typeName) {
            case 'int':
                return (int)$value;
            case 'float':
                return (float)$value;
            case 'bool':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'string':
                return (string)$value;
            default:
                return $value;
        }
    }
}

