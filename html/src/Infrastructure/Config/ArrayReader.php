<?php

declare(strict_types=1);

namespace Otus\DataMapper\Infrastructure\Config;

readonly class ArrayReader implements ReaderInterface
{
    /**
     * @param string $path
     */
    public function __construct(private string $path)
    {
    }

    /**
     * @return iterable
     */
    public function get(): iterable
    {
        return require $this->path;
    }
}
