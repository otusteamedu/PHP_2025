<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\BookService;
use App\Service\FileLoaderService;
use Throwable;

class LoadBooksCommand
{
    public function __construct(
        private BookService $bookService,
        private FileLoaderService $fileLoaderService,
    ){
    }

    public function run(): string
    {
        try {
            $file = getenv('BOOK_LIST_FILE');
            $bookList = $this->fileLoaderService->loadJsonLineListFromFile($file);

            $this->bookService->addBulk($bookList);

            return 'Success';
        } catch (Throwable $e) {
            return $e->getMessage();
        }
    }
}
