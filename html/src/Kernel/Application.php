<?php

declare(strict_types=1);

namespace Otus\Kernel;

use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Container\Container;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use Psr\Http\Message\ServerRequestInterface;

class Application
{
    /**
     * @var Application|null
     */
    private static ?Application $instance = null;

    /**
     * @param ServerRequestInterface $request
     * @param Container $container
     * @param Router $router
     */
    private function __construct(
        public readonly ServerRequestInterface $request,
        public readonly Container $container,
        public readonly Router $router,
    ) {
    }

    /**
     * @return static
     */
    public static function getInstance(): static
    {
        if (!static::$instance instanceof self) {
            $request = static::getRequest();

            $container = static::getContainer();

            $router = static::getRouter($container);

            static::$instance = new self($request, $container, $router);
        }

        return static::$instance;
    }

    public function handle(): void
    {
        new SapiEmitter()
            ->emit(
                $this->router->handle($this->request)
            );
    }

    /**
     * @return ServerRequestInterface
     */
    protected static function getRequest(): ServerRequestInterface
    {
        return ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES,
        );
    }

    /**
     * @return Container
     */
    protected static function getContainer(): Container
    {
        return new Container();
    }

    /**
     * @param Container $container
     *
     * @return Router
     */
    protected static function getRouter(Container $container): Router
    {
        $router = new Router();
        $strategy = new ApplicationStrategy();

        $strategy->setContainer($container);

        $router->setStrategy($strategy);

        return $router;
    }
}
