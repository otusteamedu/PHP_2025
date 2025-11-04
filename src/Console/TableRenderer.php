<?php

namespace App\Console;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

class TableRenderer
{
    public function render(array $results): void
    {
        $output = new ConsoleOutput();
        $table = new Table($output);

        $headers = ['Название', 'sku', 'Категория', 'Цена', 'В наличие на Мира', 'В наличии на Ленина'];
        $table->setHeaders($headers);

        $hits = $results['hits']['hits'] ?? [];

        foreach ($hits as $hit) {
            $source = $hit['_source'];
            $stocks = $source['stock'] ?? [];

            $stockInShop1 = $stocks[0]['stock'] ?? '';
            $stockInShop2 = $stocks[1]['stock'] ?? '';

            $table->addRow([
                $source['title'] ?? '',
                $source['sku'] ?? '',
                $source['category'] ?? '',
                $source['price'] ?? '',
                $stockInShop1,
                $stockInShop2,
            ]);
        }

        $table->render();
    }
}
