<?php

declare(strict_types=1);

namespace Otus\Code\Application\Order\Builder;

use Otus\Code\Application\Product\Strategy\DefaultProductCreationStrategy;
use Otus\Code\Application\Product\Recipe\ProductCustomizer;
use Otus\Code\Application\Product\Recipe\Recipe;
use Otus\Code\Application\Product\Strategy\ProductCreationStrategyInterface;
use Otus\Code\Application\Product\Strategy\ProductContext;
use Otus\Code\Domain\Order\Entity\Order;
use Otus\Code\Domain\Order\Enum\OrderStatus;
use Otus\Code\Domain\Order\Observer\OrderObserverInterface;

final class OrderBuilder
{
    private ?Order $order = null;

    private readonly ProductCustomizer $customizer;

    private readonly ProductContext $productContext;

    public function __construct(
        ProductCustomizer $customizer,
        ?ProductContext $productContext = null,
    ) {
        $this->customizer = $customizer;
        $this->productContext = $productContext ?? new ProductContext(new DefaultProductCreationStrategy());
    }

    public function start(string $orderId): self
    {
        $this->order = new Order($orderId);

        return $this;
    }

    public function subscribe(OrderObserverInterface $observer): self
    {
        $this->ensureOrder();
        $this->order->attach($observer);

        return $this;
    }

    public function addRecipeItem(ProductCreationStrategyInterface $strategy, Recipe $recipe): self
    {
        $this->ensureOrder();

        $this->productContext->useStrategy($strategy);
        $product = $this->productContext->createBaseProduct();
        $product = $this->customizer->applyRecipe($product, $recipe);

        $this->order->addItem($product);

        return $this;
    }

    /**
     * @param class-string[] $decorators
     */
    public function addCustomItem(ProductCreationStrategyInterface $strategy, array $decorators): self
    {
        $this->ensureOrder();

        $this->productContext->useStrategy($strategy);
        $product = $this->productContext->createBaseProduct();
        $product = $this->customizer->applyCustomIngredients($product, $decorators);

        $this->order->addItem($product);

        return $this;
    }

    public function moveTo(OrderStatus $status): self
    {
        $this->ensureOrder();
        $this->order->changeStatus($status);

        return $this;
    }

    public function build(): Order
    {
        $this->ensureOrder();

        return $this->order;
    }

    private function ensureOrder(): void
    {
        if ($this->order === null) {
            throw new \Exception('Order must be started before configuration.');
        }
    }
}
