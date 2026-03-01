<?php

declare(strict_types=1);

namespace App\Infrastructure\Container;

use App\Application\UseCases\CreateReportRequestUseCase;
use App\Domain\Interfaces\MessagePublisherInterface;
use App\Domain\Interfaces\ReportRequestServiceInterface;
use App\Infrastructure\MessageQueue\RabbitMQConnection;
use App\Infrastructure\MessageQueue\RabbitMQPublisher;
use App\Infrastructure\Services\ReportRequestQueueService;
use App\Presentation\Controllers\Actions\CreateReportAction;
use App\Presentation\Controllers\Actions\IndexAction;
use App\Presentation\Controllers\Controller;

class ContainerBuilder
{
    public static function build(): Container
    {
        $container = new Container();

        $container->singleton(RabbitMQConnection::class, fn() => new RabbitMQConnection());

        $container->singleton(MessagePublisherInterface::class, fn(Container $c) => new RabbitMQPublisher(
            $c->get(RabbitMQConnection::class)
        ));

        $container->singleton(ReportRequestServiceInterface::class, fn(Container $c) => new ReportRequestQueueService(
            $c->get(MessagePublisherInterface::class)
        ));

        $container->singleton(CreateReportRequestUseCase::class, fn(Container $c) => new CreateReportRequestUseCase(
            $c->get(ReportRequestServiceInterface::class)
        ));

        $container->singleton(IndexAction::class, fn() => new IndexAction());

        $container->singleton(CreateReportAction::class, fn(Container $c) => new CreateReportAction(
            $c->get(CreateReportRequestUseCase::class)
        ));

        $container->singleton(Controller::class, fn(Container $c) => new Controller([
            $c->get(IndexAction::class),
            $c->get(CreateReportAction::class),
        ]));

        return $container;
    }
}
