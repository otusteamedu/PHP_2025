<?php

use App\Food\Domain\Repository\FoodOrderRepositoryInterface;
use App\Food\Domain\Repository\FoodRepositoryInterface;
use App\Food\Infrastructure\Repository\FoodOrderRepository;
use App\Food\Infrastructure\Repository\FoodRepository;
use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Infrastructure\Repository\UserRepository;

$builder = new \DI\ContainerBuilder();
$builder->useAutowiring(true);
$builder->useAttributes(true);
$builder->addDefinitions(
    [
        FoodOrderRepositoryInterface::class => DI\get(FoodOrderRepository::class),
        UserRepositoryInterface::class => DI\get(UserRepository::class),
        FoodRepositoryInterface::class => DI\get(FoodRepository::class),
    ]
);

return $builder->build();