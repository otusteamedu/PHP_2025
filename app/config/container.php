<?php

use App\Food\Domain\Repository\FoodOrderRepositoryInterface;
use App\Food\Domain\Repository\FoodRepositoryInterface;
use App\Food\Infrastructure\Repository\FoodOrderRepository;
use App\Food\Infrastructure\Repository\FoodRepository;
use App\Shared\Application\Publisher\PublisherInterface;
use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Infrastructure\Repository\UserRepository;

$publisher = require(dirname(__DIR__) . '/config/publisher.php');

$builder = new \DI\ContainerBuilder();
$builder->useAutowiring(true);
$builder->useAttributes(true);
$builder->addDefinitions(
    [
        FoodOrderRepositoryInterface::class => DI\get(FoodOrderRepository::class),
        UserRepositoryInterface::class => DI\get(UserRepository::class),
        FoodRepositoryInterface::class => DI\get(FoodRepository::class),
        PublisherInterface::class => $publisher,
    ]
);

return $builder->build();