<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Kernel;

use Otus\Queue\Infrastructure\Component\Collection;
use Otus\Queue\Infrastructure\Dic\Container;

abstract class AbstractKernel
{
    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->setContainer(
            Collection::make($config)->wrap('container', [])
        );
    }

    /**
     * @param Collection $container
     */
    protected function setContainer(Collection $container): void
    {
        $singletons = $container->wrap('singletons', []);
        $definitions = $container->wrap('definitions', []);

        foreach ($singletons as $class => $callback) {
            Container::getInstance()->setSingleton($class, $callback);
        }

        foreach ($definitions as $class => $callback) {
            Container::getInstance()->setDefinition($class, $callback);
        }
    }
}
