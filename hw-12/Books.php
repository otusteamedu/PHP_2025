<?php

declare(strict_types=1);

class Books
{
    /**
     * @param list<Book> $books
     */
    public function __construct(
        public array $books
    ) {
    }
}
