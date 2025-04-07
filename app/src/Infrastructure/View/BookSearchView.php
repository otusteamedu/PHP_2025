<?php
declare(strict_types=1);


namespace App\Infrastructure\View;

use App\Domain\Repository\Pager;
use Console_Table;

class BookSearchView
{
    private Console_Table $table;

    public function __construct()
    {
        $this->table = new Console_Table();
    }

    public function buildTable(array $books, Pager $pager): string
    {
        $this->table->setHeaders(['#', 'Title', 'Sku', 'Price', 'Category', 'In stock']);
        foreach ($books as $key => $book) {
            $this->table->addRow([
                ++$key, $book->getTitle(), $book->getSku(), $book->getPrice(), $book->getCategory(), $book->inStock()
            ]);
        }

        return sprintf('Всего книг: %d, страница %d из %d, на странице %d.',
                $pager->total_items, $pager->page, $pager->total_pages, $pager->per_page)
            . PHP_EOL . $this->table->getTable();
    }

}