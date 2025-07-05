<?php declare(strict_types=1);

namespace App;

use App\Builder\FoodBuilder;
use App\Builder\FoodDirector;
use App\Chain\CookHandler;
use App\Chain\PrepareHandler;
use App\Chain\ServeHandler;
use App\Core\FoodProductInterface;
use App\Core\FoodType;
use App\Decorator\CheeseDecorator;
use App\Decorator\LettuceDecorator;
use App\Decorator\OnionDecorator;
use App\Proxy\CookingProxy;
use App\Strategy\BurgerStrategy;
use App\Strategy\HotDogStrategy;
use App\Strategy\SandwichStrategy;

class App
{
    public function cook(FoodType $type): FoodProductInterface
    {
        $product = match ($type) {
            FoodType::BURGER => $this->cookBurger(),
            FoodType::SANDWICH => $this->cookSandwich(),
            FoodType::HOT_DOG => $this->cookHotDog(),
            default => throw new \Exception("Unsupported type"),
        };

        return $product;
    }

    public function cookBurger (): FoodProductInterface
    {
        $prepare = new PrepareHandler();
        $cook = new CookHandler();
        $serve = new ServeHandler();

        $prepare->setNext($cook)->setNext($serve);

        $strategy = new BurgerStrategy();
        $builder = new FoodBuilder();
        $director = new FoodDirector();

        $product = $director->buildClassicCheeseBurger($builder, $strategy);

        $proxy = new CookingProxy();
        $proxy->process($product);

        $prepare->handle($product);

        return $product;
    }

    public function cookHotDog(): FoodProductInterface
    {
        $prepare = new PrepareHandler();
        $cook = new CookHandler();
        $serve = new ServeHandler();
        $prepare->setNext($cook)->setNext($serve);

        $strategy = new HotDogStrategy();
        $builder = new FoodBuilder();

        $builder->setBase($strategy->createProduct());
        $builder->addIngredient(fn($p) => new CheeseDecorator($p));
        $builder->addIngredient(fn($p) => new OnionDecorator($p));

        $product = $builder->getProduct();

        $proxy = new CookingProxy();
        $proxy->process($product);

        $prepare->handle($product);

        return $product;
    }

    public function cookSandwich(): FoodProductInterface
    {
        $prepare = new PrepareHandler();
        $cook = new CookHandler();
        $serve = new ServeHandler();
        $prepare->setNext($cook)->setNext($serve);

        $strategy = new SandwichStrategy();
        $builder = new FoodBuilder();

        $builder->setBase($strategy->createProduct());
        $builder->addIngredient(fn($p) => new LettuceDecorator($p));
        $builder->addIngredient(fn($p) => new CheeseDecorator($p));
        $builder->addIngredient(fn($p) => new OnionDecorator($p));

        $product = $builder->getProduct();

        $proxy = new CookingProxy();
        $proxy->process($product);

        $prepare->handle($product);

        return $product;
    }
}
