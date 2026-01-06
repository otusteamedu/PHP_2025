<?php

declare(strict_types=1);

readonly class BookRepository
{
    public function __construct(
        private BookMapper $mapper
    ) {
    }

    public function getBookById(int $id): ?Book
    {
        return $this->mapper->findById($id);
    }

    public function getBooksByAuthor(string $author): Books
    {
        return $this->mapper->findByAuthor($author);
    }

    public function createBook(string $title, string $author): void
    {
        $this->mapper->insert($title, $author);
    }
}
