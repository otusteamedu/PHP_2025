<?php

declare(strict_types=1);

namespace App\Core\Http\Controller\Factory;

interface ControllerFactoryInterface
{
    public function createHttpController(string $className): object;
}
