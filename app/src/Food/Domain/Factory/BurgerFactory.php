<?php
declare(strict_types=1);


namespace App\Food\Domain\Factory;

use App\Food\Domain\Aggregate\Burger\Burger;
use App\Food\Domain\Aggregate\Food;
use App\Food\Domain\Aggregate\VO\FoodTitle;
use App\Shared\Application\Publisher\PublisherInterface;

readonly class BurgerFactory implements FoodFactoryInterface
{
    public function __construct(private PublisherInterface $publisher)
    {
    }

    public function build(string $orderId, ?string $title): Food
    {
        if ($title === null) {
            return new Burger($orderId, $this->publisher);
        }
        return new Burger($orderId, $this->publisher, new FoodTitle($title));
    }
}