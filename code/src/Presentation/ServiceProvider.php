<?php

declare(strict_types=1);

namespace App\Presentation;

use App\Application\UseCases\CreateOrderUseCase;
use App\Application\UseCases\GetMenuUseCase;
use App\Application\UseCases\GetOrderUseCase;
use App\Application\UseCases\GetOrderStatusHistoryUseCase;
use App\Application\UseCases\CancelOrderUseCase;
use App\Infrastructure\Container\Container;
use App\Presentation\Controllers\Actions\CreateOrderAction;
use App\Presentation\Controllers\Actions\GetMenuAction;
use App\Presentation\Controllers\Actions\GetOrderAction;
use App\Presentation\Controllers\Actions\GetOrderStatusHistoryAction;
use App\Presentation\Controllers\Actions\CancelOrderAction;
use App\Presentation\Controllers\Actions\IndexAction;
use App\Presentation\Controllers\Controller;

class ServiceProvider
{
    public static function register(Container $container): void
    {
        $container->set(IndexAction::class, fn() => new IndexAction());

        $container->set(GetMenuAction::class, function (Container $c) {
            return new GetMenuAction(
                $c->get(GetMenuUseCase::class)
            );
        });

        $container->set(CreateOrderAction::class, function (Container $c) {
            return new CreateOrderAction(
                $c->get(CreateOrderUseCase::class)
            );
        });

        $container->set(GetOrderAction::class, function (Container $c) {
            return new GetOrderAction(
                $c->get(GetOrderUseCase::class)
            );
        });

        $container->set(GetOrderStatusHistoryAction::class, function (Container $c) {
            return new GetOrderStatusHistoryAction(
                $c->get(GetOrderStatusHistoryUseCase::class)
            );
        });

        $container->set(CancelOrderAction::class, function (Container $c) {
            return new CancelOrderAction(
                $c->get(CancelOrderUseCase::class)
            );
        });

        $container->set(Controller::class, function (Container $c) {
            return new Controller([
                $c->get(IndexAction::class),
                $c->get(GetMenuAction::class),
                $c->get(CreateOrderAction::class),
                $c->get(GetOrderStatusHistoryAction::class),
                $c->get(CancelOrderAction::class),
                $c->get(GetOrderAction::class),
            ]);
        });
    }
}
