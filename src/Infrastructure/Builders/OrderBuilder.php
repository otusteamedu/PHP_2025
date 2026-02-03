<?php

namespace App\Infrastructure\Builders;

use App\Domain\Interfaces\OrderBuilderInterface;
use App\Domain\Entities\Order;
use App\Domain\Entities\Product;
use App\Domain\Enums\ProductType;
use App\Infrastructure\Strategies\ProductPrototypeFactory;
use App\Infrastructure\Decorators\RecipeDecorator;

class OrderBuilder implements OrderBuilderInterface
{
    private ?Order $order = null;
    private ProductPrototypeFactory $prototypeFactory;
    private RecipeDecorator $decorator;

    public function __construct(
        ProductPrototypeFactory $prototypeFactory,
        RecipeDecorator $decorator
    ) {
        $this->prototypeFactory = $prototypeFactory;
        $this->decorator = $decorator;
    }

    public function createOrder(): self
    {
        $this->order = new Order();
        return $this;
    }

    public function addProduct(string $productType, array $ingredients = []): self
    {
        if (!$this->order) {
            throw new \RuntimeException('Order not created');
        }

        $type = ProductType::from(strtolower($productType));
        $strategy = $this->prototypeFactory->getStrategy($type);
        
        $product = $strategy->createPrototype();
        $product = $this->decorator->decorate($product, $ingredients);
        
        $this->order->addProduct($product);
        
        return $this;
    }

    public function setCustomerEmail(string $email): self
    {
        if ($this->order) {
            $this->order->setCustomerEmail($email);
        }
        
        return $this;
    }

    public function getOrder(): Order
    {
        if (!$this->order) {
            throw new \RuntimeException('Order not created');
        }
        
        return $this->order;
    }
}