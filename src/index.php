<?php

spl_autoload_register();

use Application\UseCase\MakeProduct\MakeProductUseCase;
use Domain\Entity\Product\BurgerProduct;
use Domain\Entity\Product\HotdogProduct;

$product = (
    new MakeProductUseCase(new BurgerProduct("Стандартный рецепт"))
)();

echo $product."</br>";

$product = (
    new MakeProductUseCase(new BurgerProduct("Улучшенный рецепт"))
)();

echo $product."</br>";


$product = (
    new MakeProductUseCase(new HotdogProduct("Особый рецепт"))
)();

echo $product."</br>";
