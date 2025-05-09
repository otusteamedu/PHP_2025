<?php

spl_autoload_register();
session_start();

use Application\UseCase\MakeProduct\MakeProductUseCase;
use Application\UseCase\MakeOrder\MakeOrderUseCase;
use Application\UseCase\MakeOrderCheckout\MakeOrderCheckoutUseCase;
use Domain\Entity\Product\BurgerProduct;
use Domain\Entity\Product\BurgerProductExt;
use Domain\Entity\Product\HotdogProduct;
use Domain\Entity\Product\HotdogProductExt;
use Domain\Entity\Product\ProductExt;


use Domain\Entity\Order\Order;
use Domain\ValueObject\User;
use Domain\ValueObject\Product;


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

    $product[] = (
        new MakeProductUseCase(
            new ProductExt(
                new BurgerProduct("Добавьте огурчиков и лука побольше")
            )
        )
    )();



 
    $order_data = (
        new MakeOrderUseCase(
            $order = new Order(
                new User(1),
                new Product($product)
            )
        )
    )();

    print_r($order_data);

    echo "<hr/>";

    $order->setStatus("Awaiting... 2 minute");

    $order_data = (
        new MakeOrderUseCase(
            $order
        )
    )();

    print_r($order_data);

    echo "<hr/>";

    $order->setStatus("Awaiting... 1 minute");

    $order_data = (
        new MakeOrderUseCase(
            $order
        )
    )();

    print_r($order_data);

}


if(isset($_GET["order_checkout"])) {

    $product_array[] = (
        new MakeProductUseCase(
            new ProductExt(
                new BurgerProduct("Добавьте огурчиков и лука побольше")
            )
        )
    )();

    $product_array[] = (
        new MakeProductUseCase(
            new BurgerProduct("")
        )
    )();

    $product = new Product($product_array); // Заполняем продукт
    $user = new User(1); // Авторизуем пользователя
    $order = new Order($user, $product); // Размещаем заказ
    $payway = "Картой"; // Способ оплаты
    $getway = "На кассе"; // Способ получения

    $res = (new MakeOrderCheckoutUseCase($order,$payway,$getway))(); // Формируем заказ

    echo "<pre>";
    print_r($res);
    echo "</pre>";

}