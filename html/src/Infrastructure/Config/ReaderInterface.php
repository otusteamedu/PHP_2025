<?php

declare(strict_types=1);

namespace Otus\DataMapper\Infrastructure\Config;

interface ReaderInterface
{
    /**
     * @return iterable
     */
    public function get(): iterable;
}
