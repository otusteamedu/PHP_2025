<?php
declare(strict_types=1);

require __DIR__ . '/../composer/vendor/autoload.php';

use App\Application\UseCase\AddIngredientUseCase;
use App\Application\UseCase\CreateOrderUseCase;
use App\Application\UseCase\CreateProductUseCase;
use App\Application\UseCase\TrackCookingProcessUseCase;
use App\Domain\Entity\Event\Status\NotificationCookingProcess;
use App\Domain\Entity\Event\Status\SubscribesCookingProcess;
use App\Domain\Entity\Ingredient\IngredientProduct;
use App\Domain\Entity\Products\ProductCollection;
use App\Domain\Entity\Sale\Cost\DiscountedPrice;
use App\Domain\Entity\Sale\Cost\ExtraCharge;
use App\Domain\Entity\Sale\Cost\OriginalCost;
use App\Domain\Patterns\AbstractFactory\BurgerFactory;
use App\Domain\Patterns\AbstractFactory\HotDogFactory;
use App\Domain\Patterns\AbstractFactory\SandwichFactory;
use App\Domain\Patterns\Decorator\CheeseIngredientDecorator;
use App\Domain\Patterns\Decorator\HamIngredientDecorator;
use App\Domain\Patterns\Decorator\LettuceIngredientDecorator;
use App\Domain\Patterns\Decorator\OnionIngredientDecorator;
use App\Domain\Patterns\Decorator\PepperIngredientDecorator;
use App\Domain\Patterns\Observer\Publisher;

// ------------------------------------ Абстрактная фабрика -------------------------------------

$originalBurger = new CreateProductUseCase((new BurgerFactory())->createOriginal());
$originalBurger();

$spicyBurger = new CreateProductUseCase((new BurgerFactory())->createSpicy());
$spicyBurger();

$originalSandwich = new CreateProductUseCase((new SandwichFactory())->createOriginal());
$originalSandwich();

$spicySandwich = new CreateProductUseCase((new SandwichFactory())->createSpicy());
$spicySandwich();

$originalHotDog = new CreateProductUseCase((new HotDogFactory())->createOriginal());
$originalHotDog();

$spicyHotDog = new CreateProductUseCase((new HotDogFactory())->createSpicy());
$spicyHotDog();

echo '<br><br>';

// ----------------------------------------- Декоратор ------------------------------------------

if ($burger = $originalBurger->getProduct()) {

    $ingredientsProduct = new AddIngredientUseCase(new HamIngredientDecorator(
            new CheeseIngredientDecorator(
                new PepperIngredientDecorator(
                    new OnionIngredientDecorator(
                        new LettuceIngredientDecorator(new IngredientProduct($burger))
                    )
                )
            )
        )
    );

    $ingredientsProduct();
    $burger->setIngredient($ingredientsProduct->getIngredient());
}

echo '<br><br>';

// ---------------------------------------- Наблюдатель -----------------------------------------

$publisher = new Publisher();
$publisher->subscribe(new SubscribesCookingProcess());
$publisher->subscribe(new NotificationCookingProcess());

$createSandwich = new CreateProductUseCase((new SandwichFactory())->createSpicy(), $publisher);
$createSandwich();

echo '<br><br>';

// ------------------------------------------- Прокси -------------------------------------------

$originalHotDog = new CreateProductUseCase((new HotDogFactory())->createOriginal());
$originalHotDog();
if ($hotDot = $originalHotDog->getProduct()) {
    $trackCookingProcessUseCase = new TrackCookingProcessUseCase($hotDot);
    $trackCookingProcessUseCase();
}

echo '<br><br>';

//// ------------------------------------------ Стратегия -----------------------------------------

$products = new ProductCollection();
$products->addProduct($burger);
$products->addProduct($originalSandwich->getProduct());
$products->addProduct($spicyHotDog->getProduct());

$createOrder = new CreateOrderUseCase($products, new OriginalCost());
$createOrder();


$products = new ProductCollection();
$products->addProduct($spicyBurger->getProduct());
$products->addProduct($originalHotDog->getProduct());

$createOrder = new CreateOrderUseCase($products, new DiscountedPrice(15));
$createOrder();


$products = new ProductCollection();
$products->addProduct($originalBurger->getProduct());

$priceDelivery = 100;

$createOrder = new CreateOrderUseCase($products, new ExtraCharge($priceDelivery));
$createOrder();

// ----------------------------------------------------------------------------------------------
