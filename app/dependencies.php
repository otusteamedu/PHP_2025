<?php

declare(strict_types=1);

use App\Application\Gateway\InternetGatewayInterface;
use App\Application\Gateway\ReportGatewayInterface;
use App\Application\Settings\SettingsInterface;
use App\Domain\Factory\NewsFactoryInterface;
use App\Infrastructure\Factory\NewsFactory;
use App\Infrastructure\Gateway\InternetGateway;
use App\Infrastructure\Gateway\ReportGateway;
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
        SQLite3::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $dbSettings = $settings->get('db');
            return new SQLite3($dbSettings['database']);
        },
        InternetGatewayInterface::class => function () {
            return new InternetGateway();
        },
        ReportGatewayInterface::class => function () {
            return new ReportGateway();
        },
        NewsFactoryInterface::class => function () {
            return new NewsFactory();
        },
    ]);
};
