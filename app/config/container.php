<?php

use App\Food\Domain\Repository\FoodOrderRepositoryInterface;
use App\Food\Infrastructure\Repository\FoodOrderRepository;

$builder = new \DI\ContainerBuilder();
$builder->useAutowiring(true);
$builder->useAttributes(true);
$builder->addDefinitions([
    FoodOrderRepositoryInterface::class => DI\get(FoodOrderRepository::class),
]);

return $builder->build();