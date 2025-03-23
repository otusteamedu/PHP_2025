<?php

namespace app\Controllers;

use app\Services\BookService;

class BookController
{
    private BookService $bookService;

    public function __construct(BookService $bookService) {
        $this->bookService = $bookService;
    }

    public function createIndex($indexFile) {
        echo $this->bookService->createIndex($indexFile);
    }

    public function bulkInsert($contentFile) {
        echo $this->bookService->bulkInsert($contentFile);
    }

    public function searchBooks() {
        echo "Введите название книги (часть названия): ";
        $title = trim(fgets(STDIN));

        echo "Выберите магазин (Ленина, Мира): ";
        $shop = trim(fgets(STDIN));
        if (!in_array($shop, ['Ленина', 'Мира'])) {
            die("Неверный магазин. Допустимые значения: Ленина, Мира.");
        }

        echo "Цена не более: ";
        $price = intval(trim(fgets(STDIN)));

        echo "Необходимое количество книг: ";
        $stock = intval(trim(fgets(STDIN)));

        $result = $this->bookService->searchBooks($title, $shop, $price, $stock);

        if (isset($result['hits']['total']['value']) && $result['hits']['total']['value'] > 0) {
            echo "\nРезультаты поиска:\n";
            print_r($result['hits']['hits']);
        } else {
            echo "\nПоиск не дал результатов. Попробуйте изменить параметры.\n";
            echo "Хотите попробовать снова? (да/нет): ";
            $retry = trim(fgets(STDIN));
            if (strtolower($retry) === 'да') {
                $this->searchBooks();
            } else {
                echo "Поиск завершён.\n";
            }
        }
    }
}