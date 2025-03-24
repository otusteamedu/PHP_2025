<?php declare(strict_types=1);

namespace App\Views;

use App\Models\Book;
use LucidFrame\Console\ConsoleTable;

/**
 * Class BooksTableView
 * @package App\Views
 */
class BooksTableView
{
    /**
     * @param Book[] $books
     * @return void
     */
    public function render(array $books): void
    {
        $table = new ConsoleTable();
        $table->setHeaders(array_values($this->getDisplayedAttributes()));

        foreach ($books as $book) {
            $row = [];
            foreach ($this->getDisplayedAttributes() as $attribute => $name) {
                $value = $book->$attribute;
                if ($attribute == 'stock' && is_array($value)) {
                    $stocks = $value;
                    $value = implode('; ', array_map(function ($stock) {
                        return $stock['shop'] . ': ' . $stock['stock'];
                    }, $stocks));
                }
                $row[] = $value;
            }

            $table->addRow($row);
        }

        $table->display();
    }

    /**
     * @return string[]
     */
    private function getDisplayedAttributes(): array
    {
        return [
            'sku' => 'Артикул',
            'title' => 'Название',
            'price' => 'Цена',
            'category' => 'Категория',
            'stock' => 'Наличие',
        ];
    }
}
