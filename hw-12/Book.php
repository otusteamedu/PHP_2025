<?php

declare(strict_types=1);

class Book
{
    public function __construct(
        public int $id,
        public string $title,
        public string $author,
    ) {
    }
}
