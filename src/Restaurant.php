<?php

declare(strict_types=1);

namespace App;

use App\Adapters\Pizza;
use App\Adapters\PizzaAdapter;
use App\Builders\BurgerBuilder;
use App\Factories\BurgerFactory;
use App\Factories\HotdogFactory;
use App\Factories\SandwichFactory;
use App\Products\ProductInterface;
use App\Strategies\Order;
use App\Strategies\OrderStrategyInterface;
use App\Strategies\PremiumOrderStrategy;
use App\Strategies\StandardOrderStrategy;

class Restaurant
{
    public function __construct(
        private BurgerFactory $burgerFactory,
        private SandwichFactory $sandwichFactory,
        private HotdogFactory $hotdogFactory,
        private BurgerBuilder $burgerBuilder,
        private StandardOrderStrategy $standardStrategy,
        private PremiumOrderStrategy $premiumStrategy
    ) {
    }

    public function createBurger(): ProductInterface
    {
        return $this->burgerFactory->createProduct();
    }

    public function createSandwich(): ProductInterface
    {
        return $this->sandwichFactory->createProduct();
    }

    public function createHotdog(): ProductInterface
    {
        return $this->hotdogFactory->createProduct();
    }

    public function createCustomBurger(array $ingredients): ProductInterface
    {
        foreach ($ingredients as $ingredient) {
            $method = 'add' . ucfirst($ingredient);
            if (method_exists($this->burgerBuilder, $method)) {
                $this->burgerBuilder->$method();
            }
        }
        
        return $this->burgerBuilder->build();
    }

    public function createDecoratedProduct(ProductInterface $product, array $decorators): ProductInterface
    {
        $decoratedProduct = $product;
        
        foreach ($decorators as $decoratorClass) {
            $decoratedProduct = new $decoratorClass($decoratedProduct);
        }
        
        return $decoratedProduct;
    }

    public function createPizza(array $toppings = []): ProductInterface
    {
        $pizza = new Pizza();
        $adapter = new PizzaAdapter($pizza);
        
        if (!empty($toppings)) {
            $adapter->addToppings($toppings);
        }
        
        return $adapter;
    }

    public function createOrder(OrderStrategyInterface $strategy): Order
    {
        return new Order($strategy);
    }

    public function createStandardOrder(): Order
    {
        return $this->createOrder($this->standardStrategy);
    }

    public function createPremiumOrder(): Order
    {
        return $this->createOrder($this->premiumStrategy);
    }
}
