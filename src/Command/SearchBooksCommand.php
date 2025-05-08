<?php

namespace App\Command;

use App\Repository\BookRepository;
use App\Console\TableRenderer;

class SearchBooksCommand
{
    private BookRepository $repository;
    private TableRenderer $renderer;

    public function __construct(BookRepository $repository, TableRenderer $renderer)
    {
        $this->repository = $repository;
        $this->renderer = $renderer;
    }

    public function execute(string $query, ?int $maxPrice = null, ?string $category = null): void
    {
        $results = $this->repository->searchBooks($query, $maxPrice, $category);
        $this->renderer->render($results);
    }
}
