<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Application\DTO;

class SearchBookResponseDTO
{
    public function __construct(
        public readonly array $books,
    ) {

    }
}