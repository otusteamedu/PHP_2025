<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use App\Infrastructure\Transport\Amqp\AmqpConnectFactory;
use App\Infrastructure\Transport\Amqp\RequestProducer;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        PDO::class => function (ContainerInterface $c) {
            $db = $c->get(SettingsInterface::class)->get('db');

            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                $db['host'],
                $db['port'],
                $db['name'],
            );

            return new PDO($dsn, $db['user'], $db['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        },
        AmqpConnectFactory::class => function (ContainerInterface $c) {
            $amqp = $c->get(SettingsInterface::class)->get('amqp');

            return new AmqpConnectFactory(
                $amqp['host'],
                $amqp['port'],
                $amqp['user'],
                $amqp['password'],
                $amqp['vhost'],
            );
        },
        RequestProducer::class => function (ContainerInterface $c) {
            $amqp = $c->get(SettingsInterface::class)->get('amqp');

            return new RequestProducer(
                $c->get(AmqpConnectFactory::class),
                $amqp['exchange'],
                $amqp['queue'],
                $amqp['routing_key'],
            );
        },
    ]);
};
