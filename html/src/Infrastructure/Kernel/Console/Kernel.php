<?php

declare(strict_types=1);

namespace Otus\Food\Infrastructure\Kernel\Console;

use Otus\Food\Infrastructure\Bus\Bus;
use Otus\Food\Infrastructure\Dic\Container;
use Otus\Food\Infrastructure\Dic\UnresolveParameterException;
use Otus\Food\Infrastructure\Kernel\AbstractKernel;
use ReflectionException;
use Throwable;

class Kernel extends AbstractKernel
{
    /**
     * @param array $args
     */
    public function handle(array $args): void
    {
        try {
            $exit = $this->run($args);
        } catch (Throwable $throwable) {
            echo $throwable->getMessage(), PHP_EOL;

            $exit = 1;
        } finally {
            exit($exit);
        }
    }

    /**
     * @param array $argv
     *
     * @return int
     *
     * @throws ReflectionException
     * @throws UnresolveHandlerException
     * @throws UnresolveParameterException
     */
    private function run(array $argv): int
    {
        $command = $argv[1] ?? null;
        $args = array_slice($argv, 2);

        $bus = Container::getInstance()->get(Bus::class);

        if (!$command || !$bus->has($command)) {
            throw new UnresolveHandlerException($command);
        }

        return $bus->handle($command, $this->cast($args));
    }

    /**
     * @param array $args
     *
     * @return array
     */
    private function cast(array $args): array
    {
        $list = [];

        foreach ($args as $arg) {
            if (is_numeric($arg)) {
                $arg = (int) $arg;
            }

            $list[] = $arg;
        }

        return $list;
    }
}
