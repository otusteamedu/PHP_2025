<?php

namespace App\Command;

use App\Loader\BookLoader;
use App\Repository\BookRepository;

class IndexBooksCommand
{
    private BookLoader $bookLoader;
    private BookRepository $bookRepository;
    private string $filePath;

    public function __construct(BookLoader $bookLoader, BookRepository $bookRepository)
    {
        $this->bookLoader = $bookLoader;
        $this->bookRepository = $bookRepository;
    }

    public function setFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
    }

    public function execute(): void
    {
        $this->bookLoader = new BookLoader($this->filePath);
        $bulkBody = $this->bookLoader->loadBulkLines();
        $this->bookRepository->indexBooksBulk($bulkBody);
        echo "Книги успешно загружены в Elasticsearch.\n";
    }
}

