<?php
namespace Core;

use Controllers\FileController;

class Router
{
    private array $routes = [
        '/files/list' => ['GET', [FileController::class, 'listFiles']],
        '/files/get/{id}' => ['GET', [FileController::class, 'fileInfo']],
        '/files/add' => ['POST', [FileController::class, 'addFile']],
        '/directories/add' => ['POST', [FileController::class, 'addDirectory']],
        '/directories/get/{id}' => ['GET', [FileController::class, 'directoryInfo']],
    ];

    /**
     * @return array|null
     */
    public function getRoute(): ?array
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        foreach ($this->routes as $route => [$routeMethod, $handler]) {
            $pattern = preg_replace('/\{(\w+)\}/', '(\w+)', $route);
            if ($method === $routeMethod && preg_match('#^' . $pattern . '$#', $requestUri, $matches)) {
                $params = array_slice($matches, 1);
                $controller = new $handler[0]();
                $methodName = $handler[1];

                return [$controller, $methodName, $params];
            }
        }
        return null;
    }
}
