<?php

declare(strict_types=1);

namespace Otus\DataMapper\ABC;

class B
{
    /**
     * @param A $a
     */
    public function __construct(public A $a)
    {
        echo __METHOD__, PHP_EOL;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(
            $this->a->toArray(),
            [
                __CLASS__,
            ],
        );
    }
}
