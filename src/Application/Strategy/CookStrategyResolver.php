<?php

namespace App\Application\Strategy;

class CookStrategyResolver
{

    /** @var CookStrategyInterface[] */
    private array $strategies;

    public function __construct(array $strategies)
    {
        $this->strategies = $strategies;
    }

    public function resolve(ProductType $type): CookStrategyInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($type)) {
                return $strategy;
            }
        }

        throw new \RuntimeException("No strategy for type: {$type->value}");
    }
}
