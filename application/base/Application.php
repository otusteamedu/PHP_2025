<?php

namespace App\Base;

use InvalidArgumentException;
use ReflectionException;

final class Application
{
    private static ?self $app = null;
    private Routing $routing;
    /**
     * @var array<int, class-string<ApplicationInterface>>
     */
    private array $applications = [];

    private function __construct()
    {
        $this->routing = new Routing();
    }


    /**
     * @throws ReflectionException
     */
    public function run(): never
    {
        $this->boot();
        $this->routing->dispatch();
        exit();
    }

    private function boot(): void
    {
        $this->bootApplications();
        $this->bootServiceContainer();
        $this->bootRoutes();
    }

    private function bootApplications(): void
    {
        $applications = require __DIR__ . '/../applications.php';
        foreach ($applications as $application) {
            if (!is_a($application, ApplicationInterface::class, true)) {
                throw new InvalidArgumentException('Application class must implement ' . ApplicationInterface::class);
            }
            $this->applications[] = new $application();
        }
    }

    private function bootServiceContainer(): void
    {
        $serviceContainer = ServiceContainer::getInstance();
        foreach ($this->applications as $application) {
            $services = $application->registerServices();
            foreach ($services as $service => $factory) {
                $serviceContainer->set($service, $factory);
            }
        }
    }

    public static function getInstance(): self
    {
        if (!self::$app) {
            self::$app = new Application();
        }

        return self::$app;
    }

    private function bootRoutes(): void
    {
        foreach ($this->applications as $application) {
            $routes = $application->registerRoutes();
            foreach ($routes as $route => $handler) {
                $this->routing->addRoute(
                    $route,
                    $handler
                );
            }
        }
    }
}