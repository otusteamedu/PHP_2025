<?php
declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\Event\ProductIsCreatedEvent;
use App\Domain\Entity\Products\ProductInterface;
use App\Domain\Patterns\Observer\PublisherInterface;

class CreateProductUseCase
{

    public function __construct(
        private readonly ProductInterface    $product,
        private readonly ?PublisherInterface $publisher = null,
    )
    {
    }

    public function __invoke(): void
    {
        echo $this->product->getName() . ' [' . $this->product->getPrice() . ']<br>';

        if ($this->publisher !== null) {
            $event = new ProductIsCreatedEvent();
            $this->publisher->notify($event);
        }
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }
}