<?php

declare(strict_types=1);

namespace App\Core\Container\Context\Strategies;

use App\Core\Container\Context\ContextType;
use App\Core\Container\Providers\ServiceProviderInterface;

interface StrategyInterface
{
    public function supports(): bool;

    public function getContextType(): ContextType;

    /**
     * @return array<class-string<ServiceProviderInterface>>
     */
    public function getSpecificProviders(): array;
}
