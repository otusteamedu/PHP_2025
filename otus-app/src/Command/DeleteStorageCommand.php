<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\BookService;

class DeleteStorageCommand
{
    public function __construct(
        private BookService $bookService,
    ) {
    }

    public function run(): string
    {
        $this->bookService->deleteStorage();

        return 'Success';
    }
}
