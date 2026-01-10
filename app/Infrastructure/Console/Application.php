<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

use App\Application\Services\IndexService;
use App\Application\Services\SearchService;
use App\Application\Validators\SearchCriteriaValidator;
use App\Infrastructure\Console\Commands\IndexCommand;
use App\Infrastructure\Console\Commands\SearchCommand;
use App\Infrastructure\Elasticsearch\ElasticsearchBookRepository;
use App\Infrastructure\Elasticsearch\ElasticsearchClient;
use App\Infrastructure\Elasticsearch\IndexManager;
use Symfony\Component\Console\Application as SymfonyApplication;

final class Application extends SymfonyApplication
{
    /** Название приложения */
    private const string APP_NAME = 'Bookstore Search';
    
    /** Версия приложения */
    private const string APP_VERSION = '1.0.0';

    public function __construct()
    {
        parent::__construct(self::APP_NAME, self::APP_VERSION);
        
        $this->addCommands($this->createCommands());
    }

    /**
     * Создает команды приложения
     */
    private function createCommands(): array
    {
        $elasticsearchClient = new ElasticsearchClient();
        $indexManager = new IndexManager($elasticsearchClient);
        $bookRepository = new ElasticsearchBookRepository($elasticsearchClient, $indexManager);
        
        $searchCriteriaValidator = new SearchCriteriaValidator();
        
        $indexService = new IndexService($bookRepository);
        $searchService = new SearchService($bookRepository, $searchCriteriaValidator);
        
        $tableRenderer = new TableRenderer();

        return [
            new IndexCommand($indexService, $tableRenderer),
            new SearchCommand($searchService, $tableRenderer)
        ];
    }
}
