<?php

declare(strict_types=1);

namespace App\Core\Container\Providers;

use App\Core\Container\Container;

interface ServiceProviderInterface
{
    /**
     * Регистрирует сервисы в контейнере.
     */
    public function registerServices(Container $container): void;
}
