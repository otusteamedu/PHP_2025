<?php

declare(strict_types=1);

namespace Otus\DataMapper\Infrastructure\Kernel;

use Otus\DataMapper\Infrastructure\Bus\Bus;
use Otus\DataMapper\Infrastructure\Dic\Container;
use Otus\DataMapper\Infrastructure\Dic\UnresolveParameterException;
use ReflectionException;

class Console extends AbstractKernel
{
    /**
     * @param array $argv
     *
     * @return int
     *
     * @throws ReflectionException
     * @throws UnresolveParameterException
     */
    public function run(array $argv): int
    {
        $command = $argv[1] ?? null;
        $args = array_slice($argv, 2);

        $bus = Container::getInstance()->get(Bus::class);

        if (!$command || !$bus->has($command)) {
            return 1;
        }

        return $bus->handle($command, $args);
    }
}
