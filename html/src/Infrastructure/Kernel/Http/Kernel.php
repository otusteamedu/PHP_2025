<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Kernel\Http;

use Otus\Queue\Infrastructure\Component\Collection;
use Otus\Queue\Infrastructure\Dic\Container;
use Otus\Queue\Infrastructure\Dic\UnresolveParameterException;
use Otus\Queue\Infrastructure\Http\Exception\NotFoundHttpException;
use Otus\Queue\Infrastructure\Http\Exception\ServerInternalErrorHttpException;
use Otus\Queue\Infrastructure\Http\Request;
use Otus\Queue\Infrastructure\Http\Response\ResponseInterface;
use Otus\Queue\Infrastructure\Http\ResponseFactory;
use Otus\Queue\Infrastructure\Http\Route;
use Otus\Queue\Infrastructure\Http\Router;
use Otus\Queue\Infrastructure\Kernel\AbstractKernel;
use ReflectionException;
use Throwable;

class Kernel extends AbstractKernel
{
    /**
     * @var Router
     */
    private Router $router {
        get {
            return $this->router;
        }
    }

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);

        $this->registerRoutes(
            Collection::make($config)->wrap('routes', [])
        );
    }

    /**
     * @param Request $request
     */
    public function handle(Request $request): void
    {
        try {
            $response = $this->run($request);
        } catch (Throwable $throwable) {
            $response = ResponseFactory::exception($request, new ServerInternalErrorHttpException($throwable));
        }

        $response->send();
    }

    /**
     * @param Request $request
     *
     * @return ResponseInterface
     *
     * @throws UnresolveParameterException
     * @throws ReflectionException
     */
    private function run(Request $request): ResponseInterface
    {
        $resolve = $this->router->resolve($request);

        if ($resolve === null) {
            return ResponseFactory::exception($request, new NotFoundHttpException());
        }

        /** @var array{Route, array} $resolve */
        [$route, $params] = $resolve;

        $controller = Container::getInstance()->get($route->controller);

        return $controller->{$route->action}($request, ...$params);
    }

    /**
     * @param Collection $routes
     */
    private function registerRoutes(Collection $routes): void
    {
        $this->router = new Router();

        $fallback = $routes->pull('fallback');

        if (is_array($fallback)) {
            $this
                ->router
                ->fallback(
                    ...$fallback,
                );
        }

        foreach ($routes as $route) {
            $this
                ->router
                ->push(
                    ...$route,
                );
        }
    }
}
