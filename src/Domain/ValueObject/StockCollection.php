<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Domain\ValueObject;

class StockCollection
{
    private array $stockCollection;

    public function __construct(StockItem ...$stockCollection)
    {
        $this->stockCollection = $stockCollection;
    }

    public function fromArray(array $data): StockCollection
    {
        $items = [];
        foreach ($data as $row) {
            $items[] = new StockItem(
                (string)$row['shop'],
                (int)$row['stock']
            );
        }

        return new self(...$items);
    }

    public function getItems(): array
    {
        return $this->stockCollection;
    }

    public function toArray(): array
    {
        return array_map(function (StockItem $item) {
            return $item->toArray();
        }, $this->stockCollection);
    }
}