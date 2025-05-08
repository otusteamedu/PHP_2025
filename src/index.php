<?php

spl_autoload_register();

use Application\UseCase\MakeProduct\MakeProductUseCase;
use Domain\Entity\Product\BurgerProduct;
use Domain\Entity\Product\BurgerProductExt;
use Domain\Entity\Product\HotdogProduct;
use Domain\Entity\Product\HotdogProductExt;
use Domain\Entity\Product\ProductExt;


if(isset($_GET["my_products"])) {


    // Сделаем особый заказ
    $product[] = (
        new MakeProductUseCase(
            new ProductExt(
                new BurgerProduct("Добавьте огурчиков и лука побольше")
            )
        )
    )();

    // Сделаем обычный заказ
    $product[] = (
        new MakeProductUseCase(
            new BurgerProduct("")
        )
    )();

    // Сделаем обычный заказ
    $product[] = (
        new MakeProductUseCase(
            new HotdogProduct("")
        )
    )();

    // Сделаем особый заказ
    $product[] = (
        new MakeProductUseCase(
            new ProductExt(
                new HotdogProduct("Добавьте две сосиски")
            )
        )
    )();
    

    foreach($product AS $id=>$res) {
        echo "<p>{$id}: {$res}</p>";
    }



}

if(isset($_GET["order_status"])) {
    echo "Заказ ";
}