<?php

declare(strict_types=1);

namespace App\Core\Container\Context;

use App\Core\Container\Context\Strategies\StrategyInterface;

class ContextDetector
{
    private ?StrategyInterface $selectedStrategy = null;

    /**
     * @param StrategyInterface[] $strategies
     */
    public function __construct(
        private readonly array $strategies,
    ) {
    }

    public function detectStrategy(): StrategyInterface
    {
        if ($this->selectedStrategy !== null) {
            return $this->selectedStrategy;
        }

        foreach ($this->strategies as $strategy) {
            if ($strategy->supports()) {
                return $this->selectedStrategy = $strategy;
            }
        }

        throw new \LogicException('No suitable context strategy found.');
    }

    public function detectContext(): ContextType
    {
        return $this->detectStrategy()->getContextType();
    }
}
