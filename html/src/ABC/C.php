<?php

declare(strict_types=1);

namespace Otus\DataMapper\ABC;

class C
{
    /**
     * @param B $b
     * @param int $line
     */
    public function __construct(public B $b, public int $line)
    {
        echo __METHOD__, PHP_EOL;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(
            $this->b->toArray(),
            [
                $this->line,
                __CLASS__,
            ],
        );
    }
}
