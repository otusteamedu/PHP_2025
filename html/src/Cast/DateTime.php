<?php

declare(strict_types=1);

namespace Otus\DataMapper\Cast;

final readonly class DateTime implements CastInterface
{
    /**
     * @param \DateTime $value
     */
    public function __construct(private \DateTime $value)
    {
    }

    /**
     * @return string
     */
    public function getCast(): string
    {
        return $this->value->format('Y-m-d H:i:s');
    }
}
