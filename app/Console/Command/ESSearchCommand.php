<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Console\IO\ConsoleOutput;

final class ESSearchCommand extends ESCommand
{
    public function getName(): string
    {
        return 'app:es-search';
    }

    public function getDescription(): string
    {
        return 'Поиск товаров по параметрам: --q, --category, --price-lt/--price-gt, --in-stock, --size';
    }

    public function execute(array $input, ConsoleOutput $output): string
    {
        $queryString = isset($input['q']) ? (string) $input['q'] : '';
        $category = isset($input['category']) ? (string) $input['category'] : null;
        $priceLt = isset($input['price-lt']) ? (int) $input['price-lt'] : null;
        $priceGt = isset($input['price-gt']) ? (int) $input['price-gt'] : null;
        $onlyInStock = isset($input['in-stock']) ? (bool) $input['in-stock'] : false;
        $size = isset($input['size']) ? max(1, (int) $input['size']) : 10;

        if ($queryString === '' && $category === null && $priceLt === null && $priceGt === null && !$onlyInStock) {
            return $output->error('Нужно передать хотя бы --q, --category, --price-lt/--price-gt или --in-stock.');
        }

        $must = [];
        $filter = [];

        if ($queryString !== '') {
            $must[] = [
                'multi_match' => [
                    'query' => $queryString,
                    'fields' => ['title^3', 'category^2'],
                    'fuzziness' => 'AUTO',
                ],
            ];
        }

        if ($category !== null && $category !== '') {
            $filter[] = ['term' => ['category.keyword' => $category]];
        }

        if ($priceLt !== null || $priceGt !== null) {
            $range = [];
            if ($priceLt !== null) {
                $range['lt'] = $priceLt;
            }
            if ($priceGt !== null) {
                $range['gt'] = $priceGt;
            }
            $filter[] = ['range' => ['price' => $range]];
        }

        if ($onlyInStock) {
            $filter[] = ['range' => ['stock.stock' => ['gt' => 0]]];
        }

        $body = [
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => $filter,
                    ],
                ],
                'sort' => [
                    ['_score' => ['order' => 'desc']],
                    ['price' => ['order' => 'asc']],
                ],
            ],
        ];

        try {
            $response = $this->service->search($body['body']['query'], $size);
        } catch (\Throwable $e) {
            return $output->error(sprintf('Ошибка поиска: %s', $e->getMessage()));
        }

        $hits = $response['hits']['hits'] ?? [];
        if (count($hits) === 0) {
            return $output->writeln('Ничего не найдено.');
        }

        $lines = [];
        $lines[] = sprintf('%-8s | %-40s | %-20s | %-8s | %-6s', 'ID', 'Title', 'Category', 'Price', 'Stock');
        $lines[] = str_repeat('-', 8) . '-+-' . str_repeat('-', 40) . '-+-' . str_repeat('-', 20) . '-+-' . str_repeat('-', 8) . '-+-' . str_repeat('-', 6);

        foreach ($hits as $hit) {
            $src = $hit['_source'] ?? [];
            $id = $hit['_id'] ?? '';
            $title = $this->truncate((string) ($src['title'] ?? ''), 40);
            $cat = $this->truncate((string) ($src['category'] ?? ''), 20);
            $price = (string) ($src['price'] ?? '');
            $stockTotal = $this->calcStock($src['stock'] ?? []);

            $lines[] = sprintf('%-8s | %-40s | %-15s | %-8s | %-6s', $id, $title, $cat, $price, $stockTotal);
        }

        return $output->writeln(implode(PHP_EOL, $lines));
    }

    private function truncate(string $value, int $length): string
    {
        $plain = $value;

        $len = mb_strlen($plain);

        if ($len > $length) {
            return mb_substr($plain, 0, $length - 3) . '...';
        }

        return $plain . str_repeat(' ', $length - $len);
    }

    private function calcStock(array $stock): int
    {
        $total = 0;
        foreach ($stock as $row) {
            if (is_array($row) && isset($row['stock']) && is_numeric($row['stock'])) {
                $total += (int) $row['stock'];
            }
        }
        return $total;
    }
}
