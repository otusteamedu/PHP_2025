<?php

namespace Dinargab\Homework12;

use Dinargab\Homework12\Clients\ElasticSearchClient;
use Dinargab\Homework12\Index\IndexManager;
use Dinargab\Homework12\Seeder\BookSeeder;
use Dinargab\Homework12\Mapper\BookMapper;
use Dinargab\Homework12\Mapper\ConsoleMapper;
use Elastic\Elasticsearch\Client;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class App
{

    private Application $application;
    private Configuration $config;
    private Client $client;
    private BookMapper $bookMapper;
    private IndexManager $indexManager;

    public function __construct()
    {
        $this->application = new Application(getenv("APPLICATION_NAME") ?? "book-search");
        $this->config = new Configuration();
        $this->client = (new ElasticSearchClient($this->config))->getClient();
        $this->bookMapper = new BookMapper($this->client, $this->config);
        $this->indexManager = new IndexManager($this->client, $this->config);
    }

    public function init()
    {
        //Fill DB
        $this->application->register("seed-db")
            ->setCode(function (InputInterface $input, OutputInterface $output): int {
                //Creating index
                $this->indexManager->createIndex(false);
                //Seeding Index with data from books.json
                (new BookSeeder($this->client, $this->config))->seedDb();

                $output->writeln("Books successfully imported into index");
                return Command::SUCCESS;
            });

        $this->application->register("search")
            ->addArgument("query", InputArgument::REQUIRED, "Поиск по названию")
            ->addOption("category", "c", InputOption::VALUE_REQUIRED, "Фильтр по категории")
            ->addOption("min-price", null, InputOption::VALUE_REQUIRED, "Фильтр - минимальная цена", 0)
            ->addOption("max-price", null, InputOption::VALUE_REQUIRED, "Фильтр - максимальная цена")
            ->addOption("in-stock", null, InputOption::VALUE_NONE, "Показывать только товары в наличии")
            ->setCode(function (InputInterface $input, OutputInterface $output): int {

                $query = $input->getArgument("query");
                $category = $input->getOption("category");
                $minPrice = $input->getOption("min-price");
                $maxPrice = $input->getOption("max-price");
                $inStock = !empty($input->getOption("in-stock"));

                $result = $this->bookMapper->search($query, ['min_price' => $minPrice, 'max_price' => $maxPrice], $category, $inStock);

                $table = new Table($output);
                $table->setHeaders(ConsoleMapper::geTableHeaders())
                    ->addRows(ConsoleMapper::booksArrayToConsoleRows($result))
                    ->render();

                return Command::SUCCESS;
            });


        $this->application->register("get-by-sku")
            ->addArgument("sku", InputArgument::REQUIRED)
            ->setCode(function (InputInterface $input, OutputInterface $output): int {
                $bookSku = $input->getArgument("sku");
                $resultBook = $this->bookMapper->getBySku($bookSku);
                $table = new Table($output);
                $table->setHeaders(ConsoleMapper::geTableHeaders())
                    ->addRow(ConsoleMapper::bookToConsoleRow($resultBook))->render();
                return Command::SUCCESS;
            });


        $this->application->run();
    }
}
