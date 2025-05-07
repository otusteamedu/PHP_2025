<?php

namespace App;

use App\Command\IndexBooksCommand;
use App\Command\SearchBooksCommand;

class App
{
    private Config $config;
    private IndexBooksCommand $indexBooksCommand;
    private SearchBooksCommand $searchBooksCommand;

    public function __construct(
        Config $config,
        IndexBooksCommand $indexBooksCommand,
        SearchBooksCommand $searchBooksCommand
    ) {
        $this->config = $config;
        $this->indexBooksCommand = $indexBooksCommand;
        $this->searchBooksCommand = $searchBooksCommand;
    }

    public function run(array $argv): void
    {
        if (count($argv) < 2) {
            $this->printUsage();
            exit(1);
        }

        $command = $argv[1];

        switch ($command) {
            case 'index':

                $filePath = $argv[3] ?? $this->config->getBooksFile(); //если задан новый файл для Elastic, то воспользуемся им
                $this->indexBooksCommand->setFilePath($filePath);
                $this->indexBooksCommand->execute();
                break;

            case 'search':
                
                $query = $argv[2] ?? '';
                $maxPrice = isset($argv[3]) ? (int)$argv[3] : null;
                $category = isset($argv[4]) ? $argv[4] : null;
                $this->searchBooksCommand->execute($query, $maxPrice, $category);
                break;

            default:
                $this->printUsage();
                exit(1);
        }
    }

    private function printUsage(): void
    {
        echo "Usage:\n";
        echo "  php shop.php index <books.json>\n";
        echo "  php shop.php search <query>\n";
    }
}