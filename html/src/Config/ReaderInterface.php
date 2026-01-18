<?php

declare(strict_types=1);

namespace Otus\DataMapper\Config;

interface ReaderInterface
{
    /**
     * @return iterable
     */
    public function get(): iterable;
}
