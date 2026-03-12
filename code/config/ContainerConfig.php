<?php

declare(strict_types=1);

namespace Queues\Config;

use DI\Container;
use DI\ContainerBuilder;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Dotenv\Dotenv;
use Queues\Application\Interfaces\{MailerInterface,
    QueueInterface,
    RequestInterface,
    ResponseInterface,
    StatementRequestValidatorInterface};
use Queues\Application\Validators\StatementRequestValidator;
use Queues\Infrastructure\Http\{Request, Response};
use Queues\Infrastructure\Mailer\SmtpMailer;
use Queues\Infrastructure\Queue\RabbitMQ;
use Queues\Presentation\Controllers\StatementsController;

class ContainerConfig
{
    public static function getContainer(): Container
    {
        $env = self::loadEnv();

        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            LoggerInterface::class => \DI\factory(fn() => new Logger('app', [
                new StreamHandler($env['LOG_PATH'], Level::Info),
            ])),

            RequestInterface::class => \DI\autowire(Request::class),
            ResponseInterface::class => \DI\autowire(Response::class),

            QueueInterface::class => \DI\factory(fn() => new RabbitMQ(
                $env['RABBITMQ_HOST'], (int)$env['RABBITMQ_PORT'],
                $env['RABBITMQ_LOGIN'], $env['RABBITMQ_PASSWORD'],
                $env['RABBITMQ_VHOST'], $env['RABBITMQ_QUEUE']
            )),

            MailerInterface::class => \DI\factory(fn() => new SmtpMailer(
                $env['MAILER_HOST'], (int)$env['MAILER_PORT'],
                $env['MAILER_FROM'], $env['MAILER_FROM_NAME']
            )),

            StatementRequestValidatorInterface::class => \DI\autowire(
                StatementRequestValidator::class
            ),
            StatementsController::class => \DI\autowire()
                ->constructorParameter('templatePath', __DIR__ . '/../src/Presentation/View/Templates'),
        ]);

        return $builder->build();
    }

    private static function loadEnv(): array
    {
        Dotenv::createImmutable(__DIR__ . '/..')->load();

        return [
            'LOG_PATH' => $_ENV['LOG_PATH'] ?? '/var/log/app/app.log',
            'RABBITMQ_HOST' => $_ENV['RABBITMQ_HOST'] ?? 'localhost',
            'RABBITMQ_PORT' => $_ENV['RABBITMQ_PORT'] ?? 5672,
            'RABBITMQ_LOGIN' => $_ENV['RABBITMQ_LOGIN'] ?? 'guest',
            'RABBITMQ_PASSWORD' => $_ENV['RABBITMQ_PASSWORD'] ?? 'guest',
            'RABBITMQ_VHOST' => $_ENV['RABBITMQ_VHOST'] ?? '/',
            'RABBITMQ_QUEUE' => $_ENV['RABBITMQ_QUEUE'] ?? 'statements',
            'MAILER_HOST' => $_ENV['MAILER_HOST'] ?? 'localhost',
            'MAILER_PORT' => $_ENV['MAILER_PORT'] ?? 1025,
            'MAILER_FROM' => $_ENV['MAILER_FROM'] ?? 'noreply@mysite.local',
            'MAILER_FROM_NAME' => $_ENV['MAILER_FROM_NAME'] ?? 'Bank Statement Service',
        ];
    }
}
