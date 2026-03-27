<?php

declare(strict_types=1);

namespace App;

use App\Command\CreateStorageCommand;
use App\Command\DeleteStorageCommand;
use App\Command\LoadBooksCommand;
use App\Command\SearchBooksCommand;
use App\Repository\BookRepository;
use App\Service\BookService;
use App\Service\FileLoaderService;
use App\Service\TableService;
use Throwable;

class App
{
    public function run(array $argv): string
    {
        $action = $argv[1];

        $bookRepository = new BookRepository();
        $bookService = new BookService($bookRepository);

        try {
            if ($action === 'load') {
                $fileLoaderService = new FileLoaderService();
                $app = new LoadBooksCommand($bookService, $fileLoaderService);

                return $app->run();
            }

            if ($action === 'search') {
                $tableService = new TableService();
                $app = new SearchBooksCommand($bookService, $tableService);

                return $app->run($argv);
            }

            if ($action === 'create') {
                $app = new CreateStorageCommand($bookService);

                return $app->run();
            }

            if ($action === 'delete') {
                $app = new DeleteStorageCommand($bookService);

                return $app->run();
            }
        } catch (Throwable $e) {
            return $e->getMessage();
        }

        return 'Unknown action: ' . $action;
    }
}
