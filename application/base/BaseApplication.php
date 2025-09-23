<?php

namespace App\Base;

use App\Base\Routers\Routing;
use InvalidArgumentException;

use const PHP_SAPI;

final class BaseApplication
{
    private static ?self $app = null;
    private ?bool $inConsole = null;
    private Routing $routing;
    /**
     * @var array<int, class-string<ApplicationInterface>>
     */
    private array $applications = [];

    private function __construct()
    {
        $this->routing = new Routing();
    }

    public function runningInConsole(): bool
    {
        if (is_null($this->inConsole)) {
            $this->inConsole = PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg';
        }

        return $this->inConsole;
    }

    public function run(): void
    {
        $this->boot();
        $this->routing->dispatch();
    }


    private function boot(): void
    {
        $this->setErrorHandling();
        $this->bootApplications();
        $this->bootServiceContainer();
        $this->bootRoutes();
    }

    private function setErrorHandling(): void
    {
        set_error_handler([ErrorHandler::class, 'handleError']);
        set_exception_handler([ErrorHandler::class, 'handleException']);
    }

    private function bootApplications(): void
    {
        $applications = require __DIR__ . '/../applications.php';
        foreach ($applications as $application) {
            if (!is_a($application, ApplicationInterface::class, true)) {
                throw new InvalidArgumentException('Application class must implement ' . ApplicationInterface::class);
            }
            $applicationObject = new $application();
            $applicationObject->boot();
            $this->applications[] = $applicationObject;
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
            self::$app = new BaseApplication();
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