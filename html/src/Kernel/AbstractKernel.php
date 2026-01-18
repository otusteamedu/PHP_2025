<?php

declare(strict_types=1);

namespace Otus\DataMapper\Kernel;

use Otus\DataMapper\Di\Container;

abstract class AbstractKernel
{
    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->setContainer($config['container'] ?? []);
    }

    /**
     * @param array $container
     */
    protected function setContainer(array $container): void
    {
        $singletons = $container['singletons'] ?? [];
        $definitions = $container['definitions'] ?? [];

        foreach ($singletons as $class => $callback) {
            Container::getInstance()->setSingleton($class, $callback);
        }

        foreach ($definitions as $class => $callback) {
            Container::getInstance()->setDefinition($class, $callback);
        }
    }
}
