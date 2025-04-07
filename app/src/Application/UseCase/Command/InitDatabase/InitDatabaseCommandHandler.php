<?php
declare(strict_types=1);


namespace App\Application\UseCase\Command\InitDatabase;

use App\Domain\Repository\BookRepositoryInterface;
use App\Infrastructure\Repository\BookRepository;

class InitDatabaseCommandHandler
{
    private BookRepositoryInterface $bookRepository;

    public function __construct()
    {
        $this->bookRepository = new BookRepository();
    }

    /**
     * @throws \Exception
     */
    public function __invoke(InitDataBaseCommand $command): void
    {
        $result = $this->bookRepository->dbCreate($command->dbName, $command->mappings, $command->settings);
        if (!$result) {
            throw new \Exception('db create failed');
        }
    }

}