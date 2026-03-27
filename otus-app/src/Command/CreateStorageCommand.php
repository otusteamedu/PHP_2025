<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\BookService;

class CreateStorageCommand
{
    public function __construct(
        private BookService $bookService,
    ) {
    }

    public function run(): string
    {
        $this->bookService->createStorage();

        return 'Success';
    }
}
