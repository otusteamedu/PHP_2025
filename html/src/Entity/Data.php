<?php

declare(strict_types=1);

namespace Otus\Elasticsearch\Entity;

readonly class Data
{
    /**
     * @param array $data
     */
    public function __construct(
        public array $data,
    ) {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
