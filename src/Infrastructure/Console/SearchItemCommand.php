<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Infrastructure\Console;

use Dinargab\Homework14\Application\DTO\SearchBookRequestDTO;
use Dinargab\Homework14\Application\UseCase\SearchBookUseCase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:search', description: 'Search Item')]
class SearchItemCommand extends AbstractCommand
{

    public function __construct(
        private SearchBookUseCase $searchBookUseCase
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Поиск')
             ->addArgument("query", InputArgument::REQUIRED, "Поиск по названию")
             ->addOption("category", "c", InputOption::VALUE_REQUIRED, "Фильтр по категории")
             ->addOption("min-price", null, InputOption::VALUE_REQUIRED, "Фильтр - минимальная цена")
             ->addOption("max-price", null, InputOption::VALUE_REQUIRED, "Фильтр - максимальная цена")
             ->addOption("in-stock", null, InputOption::VALUE_NONE, "Показывать только товары в наличии");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $requestDto = new SearchBookRequestDTO(
            searchQuery: $input->getArgument("query"),
            minPrice: $input->getOption("min-price") !== null ? (int)$input->getOption("min-price") : null,
            maxPrice: $input->getOption("max-price") !== null ? (int)$input->getOption("max-price") : null,
            category: $input->getOption("category"),
            inStock: (bool)$input->getOption("in-stock")
        );

        $responseDto = ($this->searchBookUseCase)($requestDto);

        if (empty($responseDto->books)) {
            $output->writeln("<comment>Ничего не найдено.</comment>");
            return Command::SUCCESS;
        }

        $this->renderTable($output, $responseDto->books);

        return Command::SUCCESS;
    }
}