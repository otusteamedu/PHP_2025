<?php

declare(strict_types=1);

namespace App\Core\Container\Initialization\Initializers;

use App\Core\Container\Initialization\Payload\PayloadInterface;

interface InitializerInterface
{
    /**
     * Выполняет инициализацию и, если требуется, возвращает payload.
     */
    public function initialize(): ?PayloadInterface;
}
