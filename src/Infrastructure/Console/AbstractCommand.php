<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Infrastructure\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    protected function renderBooksTable(OutputInterface $output, array $books): void
    {
        $table = new Table($output);
        $table->setHeaders(['Title', 'SKU', 'Category', 'Price', 'Stock Info']);

        $rows = [];
        foreach ($books as $book) {
            $stockSummary = [];
            foreach ($book->stock as $item) {
                $shop           = $item['shop'];
                $qty            = $item['stock'];
                $stockSummary[] = "$shop: $qty";
            }

            $rows[] = [
                $book->title,
                $book->sku,
                $book->category,
                $book->price,
                implode(", ", $stockSummary)
            ];
        }

        $table->addRows($rows);
        $table->render();
    }
}