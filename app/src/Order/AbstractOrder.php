<?php
namespace App\Order;

abstract class AbstractOrder implements Order {

    protected array $items;

    public function __construct(array $items) {
        $this->items = $items;
    }

    public function process(): void {
        echo "=== Новый заказ ===" . PHP_EOL;
        foreach ($this->items as $item) {
            echo "- " . $item->getName() . ": " . $item->prepare() . PHP_EOL;
        }
        $this->handlePackaging();
    }

    abstract protected function handlePackaging(): void;
}
