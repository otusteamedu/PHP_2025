<?php
declare(strict_types=1);

use App\Presentation\Controller\User\UserController;
use App\Presentation\Validation\UserValidator;
use DI\Container;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Factory\AppFactory;

return function (Container $container) {
    $container->set(LoggerInterface::class, function (ContainerInterface $container) {
        $loggerSettings = [
            'name' => 'app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => Logger::DEBUG,
        ];

        $logger = new Logger($loggerSettings['name']);

        $processor = new UidProcessor();
        $logger->pushProcessor($processor);

        $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
        $logger->pushHandler($handler);

        return $logger;
    });

    $container->set(App::class, function (ContainerInterface $container) {
        AppFactory::setContainer($container);
        return AppFactory::create();
    });

    // User-related dependencies
    $container->set(UserValidator::class, function () {
        return new UserValidator();
    });

    $container->set(UserController::class, function (ContainerInterface $container) {
        return new UserController($container->get(UserValidator::class));
    });

    // TODO: Add database connection, repositories, services here
};
