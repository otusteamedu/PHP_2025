<?php

declare(strict_types=1);

namespace App\Core\Container\Initialization\Initializers;

use App\Core\Container\Config\Loaders\ModuleAggregatorLoader;
use App\Core\Container\Initialization\Payload\ModulePayload;
use App\Core\Container\Initialization\Payload\PayloadInterface;
use App\Core\Utils\PathResolverInterface;

class ModuleInitializer implements InitializerInterface
{
    public function __construct(
        private readonly PathResolverInterface $pathResolver,
    ) {
    }

    public function initialize(): ?PayloadInterface
    {
        $moduleAggregatorConfig = new ModuleAggregatorLoader($this->pathResolver)->load();

        return new ModulePayload($moduleAggregatorConfig);
    }

    public function getExpectedPayloadType(): ?string
    {
        return ModulePayload::class;
    }
}
