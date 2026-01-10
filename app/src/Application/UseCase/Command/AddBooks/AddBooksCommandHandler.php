<?php
declare(strict_types=1);


namespace App\Application\UseCase\Command\AddBooks;

use App\Domain\Repository\BookRepositoryInterface;
use App\Infrastructure\Repository\BookRepository;

class AddBooksCommandHandler
{
    private BookRepositoryInterface $bookRepository;

    public function __construct()
    {
        $this->bookRepository = new BookRepository();
    }

    /**
     * @throws \Exception
     */
    public function __invoke(AddBooksCommand $command): void
    {
        $itemsData = $this->getItems($command->filePath);
        $result = $this->bookRepository->bulkInsert($itemsData, $command->dbName);
        if ($result['errors']) {
            throw new \Exception('insert failed');
        }
    }

    /**
     * @throws \Exception
     */
    private function getItems(string $pathToFile): string
    {
        if (!file_exists($pathToFile)) {
            throw new \Exception('file not found');
        }

        return file_get_contents($pathToFile);
    }

}