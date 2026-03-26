<?php

declare(strict_types=1);

namespace Api\Config;

use Api\Domain\Interfaces\QueueInterface;
use Api\Domain\Interfaces\RepositoryInterface;
use Api\Infrastructure\Queue\RabbitMQ;
use Api\Infrastructure\Repository\Postgres;
use Api\Presentation\Api\Actions\Requests\CreateRequestAction;
use Api\Presentation\Api\Actions\Requests\GetRequestAction;
use Api\Presentation\Api\Middleware\ApiKeyAuthMiddleware;
use Api\Presentation\Api\Middleware\ErrorMiddleware;
use Api\Presentation\Api\Middleware\RequestLoggingMiddleware;
use DI\Container;
use DI\ContainerBuilder;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory as SlimAppFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Dotenv\Dotenv;

final class ContainerConfig
{
    public static function getContainer(): Container
    {
        $env = self::loadEnv();

        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            ResponseFactoryInterface::class => \DI\factory(
                fn() => new ResponseFactory()
            ),

            \PDO::class => \DI\factory(fn() => new \PDO(
                sprintf(
                    'pgsql:host=%s;port=%s;dbname=%s',
                    $env['PG_HOST'],
                    $env['PG_PORT'],
                    $env['PG_DB']
                ),
                $env['PG_USER'],
                $env['PG_PASSWORD'],
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                ]
            )),

            Logger::class => \DI\factory(function () use ($env) {
                $logger = new Logger('api');
                $handler = new StreamHandler($env['LOG_PATH'], Level::Debug);
                $handler->setFormatter(
                    new LineFormatter(
                        "[%datetime%] %channel%.%level_name%: %message% %context%\n",
                        'Y-m-d H:i:s'
                    )
                );
                $logger->pushHandler($handler);
                return $logger;
            }),

            RepositoryInterface::class => \DI\autowire(Postgres::class),

            QueueInterface::class => \DI\factory(fn() => new RabbitMQ(
                $env['RABBITMQ_HOST'],
                (int)$env['RABBITMQ_PORT'],
                $env['RABBITMQ_LOGIN'],
                $env['RABBITMQ_PASSWORD'],
                $env['RABBITMQ_VHOST'],
                $env['RABBITMQ_QUEUE']
            )),

            ApiKeyAuthMiddleware::class => \DI\factory(
                function (ResponseFactoryInterface $responseFactory) use ($env): ApiKeyAuthMiddleware {
                    $validKeys = array_filter(
                        array_map('trim', explode(',', $env['API_KEYS'])),
                        fn($key) => $key !== ''
                    );
                    if (empty($validKeys)) {
                        throw new \RuntimeException('API_KEYS не может быть пустым');
                    }
                    return new ApiKeyAuthMiddleware($responseFactory, $validKeys);
                }
            ),

            App::class => \DI\factory(function (Container $c): App {
                $app = SlimAppFactory::create();

                $app->addBodyParsingMiddleware();
                $app->addRoutingMiddleware();
                $app->add($c->get(ErrorMiddleware::class));
                $app->add($c->get(RequestLoggingMiddleware::class));
                $app->add($c->get(ApiKeyAuthMiddleware::class));

                $app->group('/api/v1', function ($group) use ($c) {
                    $group->post('/requests', $c->get(CreateRequestAction::class));
                    $group->get('/requests/{id:\d+}', $c->get(GetRequestAction::class));
                });

                return $app;
            }),
        ]);

        return $builder->build();
    }

    private static function loadEnv(): array
    {
        Dotenv::createImmutable(__DIR__ . '/..')->load();

        $required = [
            'PG_HOST',
            'PG_PORT',
            'PG_DB',
            'PG_USER',
            'PG_PASSWORD',
            'RABBITMQ_HOST',
            'RABBITMQ_PORT',
            'RABBITMQ_LOGIN',
            'RABBITMQ_PASSWORD',
            'RABBITMQ_VHOST',
            'RABBITMQ_QUEUE',
            'LOG_PATH',
            'API_KEYS',
        ];

        $result = [];
        foreach ($required as $key) {
            if (empty($_ENV[$key])) {
                throw new \RuntimeException("Отсутствует обязательная переменная окружения: {$key}");
            }
            $result[$key] = $_ENV[$key];
        }

        return $result;
    }
}
