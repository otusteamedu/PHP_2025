<?php

declare(strict_types=1);

namespace App\Routing;

use App\Controller\RequestController;
use App\Http\JsonResponse;
use App\Queue\QueuePublisherInterface;
use App\Repository\RequestRepository;
use RuntimeException;

/**
 * Роутер HTTP-запросов API
 */
final class Router
{
    private readonly JsonResponse $response;

    public function __construct(
        private readonly RequestRepository $repository,
        private readonly QueuePublisherInterface $publisher,
    ) {
        $this->response = new JsonResponse();
    }

    /**
     * Передает HTTP-запрос в подходящий метод контроллера
     *
     * @param string $method HTTP-метод
     * @param string $path Путь запроса
     *
     * @return void
     */
    public function handleRequest(string $method, string $path): void
    {
        foreach ($this->getRoutes() as $route) {
            if ($method !== $route['method']) {
                continue;
            }

            if (preg_match($route['pattern'], $path, $matches) !== 1) {
                continue;
            }

            $controller = $this->createController($route['controller']);
            $handler = $route['handler'];

            if (!method_exists($controller, $handler)) {
                throw new RuntimeException('Метод ' . $handler . ' не найден в контроллере ' . $route['controller']);
            }

            $controller->{$handler}(...$this->getRouteArguments($matches));

            return;
        }

        $this->response->send(['error' => 'Маршрут не найден'], 404);
    }

    /**
     * Возвращает таблицу маршрутов API
     *
     * @return array<int, array{method: string, pattern: string, controller: string, handler: string}>
     */
    private function getRoutes(): array
    {
        return [
            [
                'method' => 'POST',
                'pattern' => '#^/api/v1/requests$#',
                'controller' => RequestController::class,
                'handler' => 'create',
            ],
            [
                'method' => 'GET',
                'pattern' => '#^/api/v1/requests/([1-9][0-9]*)$#',
                'controller' => RequestController::class,
                'handler' => 'get',
            ],
        ];
    }

    /**
     * Создает контроллер маршрута
     *
     * @param string $controllerClass Имя класса контроллера
     *
     * @return object
     */
    private function createController(string $controllerClass): object
    {
        if (!class_exists($controllerClass)) {
            throw new RuntimeException('Контроллер ' . $controllerClass . ' не найден');
        }

        return new $controllerClass($this->repository, $this->publisher);
    }

    /**
     * Возвращает аргументы обработчика из найденного маршрута
     *
     * @param array<int, string> $matches Совпадения регулярного выражения маршрута
     *
     * @return array<int, int|string>
     */
    private function getRouteArguments(array $matches): array
    {
        array_shift($matches);

        $arguments = [];

        foreach ($matches as $match) {
            $arguments[] = ctype_digit($match) ? (int) $match : $match;
        }

        return $arguments;
    }
}
