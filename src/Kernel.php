<?php

namespace Blarkinov\Hw1500;

use Blarkinov\Hw1500\Infrastructure\Route\AsRoute;
use Framework\DI\ServiceContainer;

final class Kernel
{
    private array $routes;
    private ServiceContainer $serviceContainer;

    /**
     * @param array $config
     *
     * @throws \ReflectionException
     */
    public function __construct(
        private  array $config
    ) {
        $projectDir = __DIR__;
        $this->serviceContainer = new ServiceContainer($projectDir, $this->config);
        $this->serviceContainer->init();

        $this->routes = $this->collectRoutes();
    }

    /**
     * @return array
     *
     * @throws \ReflectionException
     */
    private function collectRoutes(): array
    {
        $routes = [];
        foreach ($this->serviceContainer->getControllers() as $controller) {
            $reflection = new \ReflectionClass($controller);

            $methods = $reflection->getMethods();
            foreach ($methods as $method) {
                $attributes = $method->getAttributes(AsRoute::class);

                foreach ($attributes as $attr) {
                    $args = $attr->getArguments();
                    $routePath = $args['path'] ?? null;

                    if (empty($routePath)) {
                        continue;
                    }

                    $routes[$routePath] = [
                        'controller' => $reflection->getName(),
                        'method' => $method->getName(),
                    ];
                }
            }
        }

        return $routes;
    }

    public function run(): void
    {
        try {
            $route = $this->resolveRoute($this->serviceContainer->getRequest()->getRequestUriBag());
            if (empty($route)) {
                return;
            }

            $controller = $this->serviceContainer->getController($route['controller']);

            $response = call_user_func([$controller, $route['method']]);

            if (!empty($response)) {
                echo $this->createJsonResponse($response);
            }
        } catch (\Exception $e) {
            echo $this->createJsonResponse([
                'result' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function resolveRoute(array $requestUriBag): array
    {
        $requestedUri = '/' . implode('/', $requestUriBag);

        if (str_contains($requestedUri, 'favico')) {
            return [];
        }

        foreach ($this->routes as $path => $route) {
            if ($requestedUri == $path) {
                return $route;
            }
        }

        throw new \Exception('Страница не найдена', 404);
    }

    public function createJsonResponse(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
