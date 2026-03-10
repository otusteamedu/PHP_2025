<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logError'            => false,
                'logErrorDetails'     => false,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                'db' => [
                    'host' => $_ENV['DB_HOST'] ?? 'postgres',
                    'port' => $_ENV['DB_PORT'] ?? '5432',
                    'name' => $_ENV['DB_NAME'] ?? 'hw20',
                    'user' => $_ENV['DB_USER'] ?? 'root',
                    'password' => $_ENV['DB_PASSWORD'] ?? 'root',
                ],
                'amqp' => [
                    'host' => $_ENV['AMQP_HOST'] ?? 'rabbitmq',
                    'port' => $_ENV['AMQP_PORT'] ?? '5672',
                    'user' => $_ENV['AMQP_USER'] ?? 'guest',
                    'password' => $_ENV['AMQP_PASSWORD'] ?? 'guest',
                    'vhost' => $_ENV['AMQP_VHOST'] ?? '/',
                    'exchange' => $_ENV['AMQP_EXCHANGE'] ?? 'requests',
                    'queue' => $_ENV['AMQP_QUEUE'] ?? 'requests',
                    'routing_key' => $_ENV['AMQP_ROUTING_KEY'] ?? 'requests',
                ],
            ]);
        }
    ]);
};
