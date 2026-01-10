<?php
declare(strict_types=1);


namespace App\Application\UseCase\Command\DeleteDatabase;

use App\Domain\Repository\BookRepositoryInterface;
use App\Infrastructure\Repository\BookRepository;

class DeleteDatabaseCommandHandler
{
    private BookRepositoryInterface $bookRepository;

    public function __construct()
    {
        $this->bookRepository = new BookRepository();
    }

    /**
     * @throws \Exception
     */
    public function __invoke(DeleteDataBaseCommand $command): void
    {
        $result = $this->bookRepository->dbDelete($command->dbName);
        if (!$result) {
            throw new \Exception('db deletion failed');
        }
    }

}