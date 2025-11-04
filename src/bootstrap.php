<?php

use App\App;
use App\Config;
use App\Loader\BookLoader;
use App\Elasticsearch\ElasticsearchClient;
use App\Repository\BookRepository;
use App\Command\IndexBooksCommand;
use App\Command\SearchBooksCommand;
use App\Console\TableRenderer;

$config = new Config();
$esClient = new ElasticsearchClient($config);
$repository = new BookRepository($esClient, $config->getIndexName()); 
$loader = new BookLoader($config->getBooksFile());
$renderer = new TableRenderer();

$indexCommand = new IndexBooksCommand($loader, $repository);
$searchCommand = new SearchBooksCommand($repository, $renderer);

$app = new App($config, $indexCommand, $searchCommand);