<?php

declare(strict_types=1);

namespace App\Core\Http\Controller\Factory;

use App\Controller\Http\ErrorController;

interface ControllerFactoryInterface
{
    public function createHttpController(string $className): object;

    public function createErrorController(): ErrorController;
}
