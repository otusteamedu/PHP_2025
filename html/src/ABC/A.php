<?php

declare(strict_types=1);

namespace Otus\DataMapper\ABC;

class A
{
    /**
     * @param bool $debug
     */
    public function __construct(public bool $debug)
    {
        echo __METHOD__, PHP_EOL;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            $this->debug,
            __CLASS__,
        ];
    }
}
