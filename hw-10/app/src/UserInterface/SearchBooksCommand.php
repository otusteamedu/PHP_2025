<?php

declare(strict_types=1);

namespace App\UserInterface;

use App\Application\SearchBooksService;
use App\Domain\Book;
use App\Domain\Shop;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'search:books')]
readonly class SearchBooksCommand
{
    public function __construct(
        private SearchBooksService $searchBooksService
    ) {
    }

    public function __invoke(InputInterface $input, OutputInterface $output, SymfonyStyle $io): int
    {
        $name = $io->ask('Введите название книги (прим. "Рыцари")');
        $category = $io->ask('Введите категорию книги (прим., "Искусство")');
        $maxPrice = (int) $io->ask('Введите максимальную цену (прим., 2000)');
        $minPrice = (int) $io->ask('Введите минимальную цену (прим., 10)', '0');

        $inStock = $io->confirm('Должна быть в наличии?');

        $output->writeln([
            '<info>Ищем книги</>',
            '<info>==============================</>',
        ]);

        $books = $this->searchBooksService->search($name, $category, $maxPrice, $minPrice, $inStock);

        if (empty($books)) {
            $io->warning("По вашему запросу книги не найдены.");
        } else {

            $io->table(
                ['Title', 'Category', 'Price', 'Stock'],
                array_map(
                    fn(Book $book) => [
                        $book->title,
                        $book->category,
                        $book->price,
                        implode(', ', array_map(
                            fn(Shop $stock) => "{$stock->name}: {$stock->countBooks}",
                            $book->stock
                        )),
                    ],
                    $books
                )
            );
        }

        return 0;
    }
}
