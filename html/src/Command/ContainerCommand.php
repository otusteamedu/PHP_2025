<?php

declare(strict_types=1);

namespace Otus\DataMapper\Command;

use Otus\DataMapper\ABC\A;
use Otus\DataMapper\ABC\C;
use Otus\DataMapper\Di\UnresolveParameterException;
use ReflectionException;

readonly class ContainerCommand
{
    /**
     * @return int
     *
     * @throws ReflectionException
     * @throws UnresolveParameterException
     */
    public function __invoke(): int
    {
        (static function (): void {
            /** @var C $c */
            $c = resolve(C::class);

            print_r($c->toArray());
        })();

        resolve(A::class)->debug = false;

        (static function (): void {
            /** @var C $c */
            $c = resolve(C::class);

            print_r($c->toArray());
        })();

        return 0;
    }
}
