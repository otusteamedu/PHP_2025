<?php

declare(strict_types=1);

namespace Otus\Elasticsearch\Entity;

readonly class Index
{
    /**
     * @param string $operation
     * @param string $index
     * @param string|int $id
     */
    public function __construct(
        public string $operation,
        public string $index,
        public string|int $id,
    ) {
    }

    /**
     * @return array[]
     */
    public function toArray(): array
    {
        return [
            $this->operation => [
                '_index' => $this->index,
                '_id' => $this->id,
            ],
        ];
    }
}
