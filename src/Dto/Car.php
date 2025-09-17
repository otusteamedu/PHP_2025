<?php
declare(strict_types = 1);

namespace Dbykov\OtusHw14\Dto;

use DBykov\SchemaSync\Attributes\Column;

final readonly class Car
{
    public function __construct(
        public int $id,
        #[Column(length: 50)]
        public string $mark,
        public string $model,
        public bool $is_active = true,
        public float $price,
    ) {}
}
