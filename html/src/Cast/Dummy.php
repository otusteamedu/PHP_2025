<?php

declare(strict_types=1);

namespace Otus\DataMapper\Cast;

final readonly class Dummy implements CastInterface
{
    /**
     * @param bool $value
     */
    public function __construct(private mixed $value)
    {
    }

    /**
     * @return bool
     */
    public function getCast(): mixed
    {
        return $this->value;
    }
}
