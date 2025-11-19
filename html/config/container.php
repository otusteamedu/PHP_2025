<?php

declare(strict_types=1);

use League\Container\Container;
use League\Container\ReflectionContainer;
use Otus\Contracts\MxServiceInterface;
use Otus\Services\MxService;

$container = new Container();
$container->delegate(new ReflectionContainer());

$container
    ->add(MxServiceInterface::class, static function (): MxServiceInterface {
        return new MxService();
    });

return $container;
