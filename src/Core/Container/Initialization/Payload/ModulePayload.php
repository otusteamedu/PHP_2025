<?php

declare(strict_types=1);

namespace App\Core\Container\Initialization\Payload;

use App\Core\Container\Config\Contracts\ConfigInterface;
use App\Core\Container\Config\Types\ModuleAggregator;

readonly class ModulePayload implements PayloadInterface
{
    /**
     * @param ModuleAggregator $moduleAggregatorConfig
     */
    public function __construct(
        public ConfigInterface $moduleAggregatorConfig,
    ) {
    }
}
