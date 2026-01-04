<?php

declare(strict_types=1);

namespace Otus\Elasticsearch\Kernel;

abstract class AbstractKernel
{
    public function __construct()
    {
        $this->env();
    }

    protected function env(): void
    {
        $path = __DIR__ . '/../../.env.php';

        if (file_exists($path)) {
            include_once $path;
        }
    }
}
