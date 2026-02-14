<?php

declare(strict_types=1);

namespace App\Presentation;

use App\Domain\Interfaces\CreateOrderUseCaseInterface;
use App\Domain\Interfaces\GetMenuUseCaseInterface;
use App\Infrastructure\Container\Container;
use App\Presentation\Controllers\Actions\CreateOrderAction;
use App\Presentation\Controllers\Actions\GetMenuAction;
use App\Presentation\Controllers\Actions\IndexAction;
use App\Presentation\Controllers\Controller;

class ServiceProvider
{
    public static function register(Container $container): void
    {
        $container->set(IndexAction::class, fn() => new IndexAction());

        $container->set(GetMenuAction::class, function (Container $c) {
            return new GetMenuAction(
                $c->get(GetMenuUseCaseInterface::class)
            );
        });

        $container->set(CreateOrderAction::class, function (Container $c) {
            return new CreateOrderAction(
                $c->get(CreateOrderUseCaseInterface::class)
            );
        });

        $container->set(Controller::class, function (Container $c) {
            return new Controller([
                $c->get(IndexAction::class),
                $c->get(GetMenuAction::class),
                $c->get(CreateOrderAction::class),
            ]);
        });
    }
}
