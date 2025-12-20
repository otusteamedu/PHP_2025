<?php
declare(strict_types=1);

namespace App\Table;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class BookTableRender
{
    /**
     * @param OutputInterface $output
     * @param array $books
     * @return void
     */
    public static function render(OutputInterface $output, array $books): void
    {
        $table = new Table($output);
        $table->setHeaders(['SKU', 'Название', 'Категория', 'Цена', 'Остаток', 'Релевантность']);

        foreach ($books as $book) {
            $table->addRow([
                $book->sku,
                $book->title,
                $book->category,
                $book->price,
                $book->totalStock,
                round($book->score, 2),
            ]);
        }

        $table->render();
    }
}
