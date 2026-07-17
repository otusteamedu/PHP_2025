<?php

declare(strict_types=1);

namespace App\Core\Container\Context\Strategies;

use App\Core\Container\Context\ContextType;
use App\Core\Container\Providers\ConsoleServiceProvider;

class CliStrategy implements StrategyInterface
{
    public function supports(): bool
    {
        return PHP_SAPI === $this->getContextType()->value;
    }

    public function getContextType(): ContextType
    {
        return ContextType::CLI;
    }

    public function getSpecificProviders(): array
    {
        return [
            ConsoleServiceProvider::class,
        ];
    }
}
