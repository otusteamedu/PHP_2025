<?php

declare(strict_types=1);

namespace App\Core\Container\Initialization\Payload;

use App\Core\Container\Config\Contracts\DotEnvConfigInterface;

readonly class CommonPayload implements PayloadInterface
{
    public function __construct(
        public DotEnvConfigInterface $dotEnvConfig,
    ) {
    }
}
