<?php

declare(strict_types=1);

use Otus\Queue\Application\Amqp\Chat;
use Otus\Queue\Application\Interface\ChatRepositoryInterface;
use Otus\Queue\Application\Persistence\DatabaseChatRepository;
use Otus\Queue\Infrastructure\Bus\Bus;
use Otus\Queue\Infrastructure\Database\Database;
use Otus\Queue\Infrastructure\Database\DatabaseInterface;
use Otus\Queue\Infrastructure\Dic\Container;
use Otus\Queue\Infrastructure\Queue\Adapter\AmqpAdapter;
use Otus\Queue\Infrastructure\Queue\Queue;
use Otus\Queue\Infrastructure\Queue\QueueInterface;
use Otus\Queue\Infrastructure\Template\Native;
use Otus\Queue\Infrastructure\Template\TemplateInterface;
use Otus\Queue\Presentation\Console\Consumer;
use Otus\Queue\Presentation\Console\Migrate;
use Otus\Queue\Presentation\Console\Publisher;
use PhpAmqpLib\Exchange\AMQPExchangeType;

return [
    'singletons' => [
        // Application
        ChatRepositoryInterface::class => static function (Container $container): ChatRepositoryInterface {
            return new DatabaseChatRepository(
                $container->get(DatabaseInterface::class),
            );
        },
        // Infrastructure
        DatabaseInterface::class => static function (): DatabaseInterface {
            return new Database(
                dsn: getenv('DATABASE_DSN'),
                username: getenv('DATABASE_USERNAME'),
                password: getenv('DATABASE_PASSWORD'),
                options: [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ],
            );
        },
        Bus::class => static function (): Bus {
            return new Bus([
                'database:migrate' => Migrate::class,
                'queue:consumer' => Consumer::class,
                'queue:publisher' => Publisher::class,
            ]);
        },
        TemplateInterface::class => static function (): TemplateInterface {
            return new Native(__DIR__ . '/../resources/views');
        },
        QueueInterface::class => static function (): QueueInterface {
            return new Queue([
                'realtime' => [
                    'adapter' => AmqpAdapter::class,
                    'config' => [
                        'connection' => [
                            'host' => getenv('QUEUE_REALTIME_HOST'),
                            'port' => getenv('QUEUE_REALTIME_PORT'),
                            'user' => getenv('QUEUE_REALTIME_USER'),
                            'password' => getenv('QUEUE_REALTIME_PASSWORD'),
                        ],
                        'queue' => [
                            'durable' => false,
                            'exclusive' => true,
                            'auto_delete' => true,
                        ],
                        'exchange' => [
                            'type' => AMQPExchangeType::FANOUT,
                            'durable' => false,
                            'auto_delete' => false,
                        ],
                        'consume' => [
                            'consumer_tag' => 'application',
                            'callback' => new Chat(),
                        ],
                    ],
                ],
            ]);
        },
        // Presentation
    ],
    'definitions' => [
        // Application
        // Infrastructure
        // Presentation
    ],
];
