<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Commands;

use App\Application\DTOs\SearchRequest;
use App\Application\Services\SearchService;
use App\Infrastructure\Console\TableRenderer;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class SearchCommand extends Command
{
    public function __construct(
        private readonly SearchService $searchService,
        private readonly TableRenderer $tableRenderer
    ) {
        parent::__construct('search');
    }

    /**
     * Настраивает параметры команды
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Поиск книг в магазине')
            ->addOption(
                'query',
                null,
                InputOption::VALUE_REQUIRED,
                'Поисковый запрос'
            )
            ->addOption(
                'category',
                null,
                InputOption::VALUE_REQUIRED,
                'Категория книг'
            )
            ->addOption(
                'max-price',
                null,
                InputOption::VALUE_REQUIRED,
                'Максимальная цена'
            )
            ->addOption(
                'in-stock',
                null,
                InputOption::VALUE_NONE,
                'Только книги в наличии'
            )
            ->setHelp('Примеры использования:' . PHP_EOL .
                '  php console.php search --query="рыцарь" --max-price=2000 --in-stock' . PHP_EOL .
                '  php console.php search --query="исторический роман" --category="Исторический роман" --max-price=1500' . PHP_EOL .
                '  php console.php search --query="фантастика" --in-stock'
            );
    }

    /**
     * Выполняет команду поиска
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $request = new SearchRequest(
                query: $input->getOption('query'),
                category: $input->getOption('category'),
                maxPrice: $this->parsePrice($input->getOption('max-price')),
                inStock: $input->getOption('in-stock')
            );

            $response = $this->searchService->search($request);

            $tableOutput = $this->tableRenderer->renderSearchResults(
                $response->getBooks(),
                $response->getTotal(),
                $response->getTook()
            );

            $output->write($tableOutput);

            return Command::SUCCESS;

        } catch (Exception $e) {
            $errorOutput = $this->tableRenderer->renderError($e->getMessage());
            $output->write($errorOutput);
            
            return Command::FAILURE;
        }
    }

    /**
     * Парсит цену из строки
     */
    private function parsePrice(?string $price): ?int
    {
        if ($price === null) {
            return null;
        }

        $price = str_replace(['₽', 'руб', 'рублей', ' '], '', $price);

        if (!is_numeric($price)) {
            throw new InvalidArgumentException('Некорректная цена: ' . $price);
        }

        return (int) $price;
    }
}