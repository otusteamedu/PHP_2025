<?php

require "vendor/autoload.php";
session_start();  

use App\Application\UseCase\MakeProduct\MakeProductUseCase;
use App\Application\UseCase\MakeOrder\MakeOrderUseCase;
use App\Application\UseCase\MakeOrderCheckout\MakeOrderCheckoutUseCase;
use App\Domain\Entity\Product\BurgerProduct;
use App\Domain\Entity\Product\BurgerProductExt;
use App\Domain\Entity\Product\HotdogProduct;
use App\Domain\Entity\Product\HotdogProductExt;
use App\Domain\Entity\Product\ProductExt;
use App\Domain\Entity\PayWay\CardPayWay;
use App\Domain\Entity\PayWay\CashPayWay;
use App\Domain\Entity\GetWay\DescGetWay;
use App\Domain\Entity\GetWay\HomeGetWay;

use App\Domain\Entity\Order\Order;
use App\Domain\ValueObject\User;
use App\Domain\ValueObject\Product;

try {


    // Добавим в заказ продукцию

    // 1. Бургер с измененным рецептом

    $product_array[] = (
        new MakeProductUseCase(
            new ProductExt(
                new BurgerProduct("Добавьте огурчиков и лука побольше")
            )
        )
    )();

    // 2. Стандартный бургер

    $product_array[] = (
        new MakeProductUseCase(
            new BurgerProduct
        )
    )();

    // 3. Стандартный Хотдог

    $product_array[] = (
        new MakeProductUseCase(
            new HotdogProduct
        )
    )();

    // 4. Хотдог с измененным рецептом

    $product_array[] = (
        new MakeProductUseCase(
            new ProductExt(
                new HotdogProduct("Добавьте две сосиски")
            )
        )
    )();

    $product = new Product($product_array); // Наша заказанная продукция
    $user = new User(1); // Авторизуем пользователя под номером 1
    $order = new Order($user, $product); // Размещаем заказ, где его id это текущая сессия пользователя
    $payway = new CardPayWay; // Выбираем способ оплаты
    $getway = new DescGetWay; // Выбираем способ доставки
    
    // Формируем заказ

    $res = (
        new MakeOrderCheckoutUseCase(
            $order,
            $payway,
            $getway
        )
    )(); 

    // Выведем данные заказа

    echo "<p><b>Ваш заказ</b></p>";
    echo "<ul>";
    foreach($res->get_order() AS $product) {
        echo "<li>$product</li>";
    }
    echo "</ul>";
    echo "<p><b>Способ оплаты:</b> {$res->get_payway()}</p>";
    echo "<p><b>Способ получения:</b> {$res->get_getway()}</p>";

    // Изменим статус

    $order->setStatus("Awaiting... 1 minute"); // Готовим заказ
    $order->setStatus("Is ready"); // Оповещаем, что готов

}

catch (\Exception $e) {
    echo $e->getMessage();
}
    