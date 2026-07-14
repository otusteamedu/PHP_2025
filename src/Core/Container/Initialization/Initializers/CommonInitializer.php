<?php

declare(strict_types=1);

namespace App\Core\Container\Initialization\Initializers;

use App\Core\Container\Config\Loaders\DotEnvLoader;
use App\Core\Container\Initialization\Payload\CommonPayload;
use App\Core\Container\Initialization\Payload\PayloadInterface;
use App\Core\Utils\PathResolverInterface;

class CommonInitializer implements InitializerInterface
{
    public function __construct(
        private readonly PathResolverInterface $pathResolver,
    ) {
    }

    public function initialize(): ?PayloadInterface
    {
        $dotEnvConfig = new DotEnvLoader($this->pathResolver)->load();

        return new CommonPayload($dotEnvConfig);
    }

    public function getExpectedPayloadType(): ?string
    {
        return CommonPayload::class;
    }
}
