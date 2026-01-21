<?php

use App\ES\Repository\BookRepository;
use Dotenv\Dotenv;

$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../../');
require $_SERVER['DOCUMENT_ROOT'] . '/public/init/bootstrap.php';

/* Названия для аргументов */
$optionNamePriceMin = '--price-min';
$optionNamePriceMax = '--price-max';
$optionNameCategory = '--category';
$optionNameTitle    = '--title';
$optionNameShop     = '--shop';

function getNumericOption(array $argv, string $name): ?float
{
    foreach ($argv as $i => $arg) {
        if ($arg === $name) {
            $value = $argv[$i + 1] ?? null;

            if ($value === null || !is_numeric($value)) {
                fwrite(STDERR, "Ошибка: {$name} ожидает числовое значение.\n");
                exit(1);
            }

            $number = (float)$value;

            if ($number < 0) {
                fwrite(STDERR, "Ошибка: {$name} ожидает неотрицательное число.\n");
                exit(1);
            }

            return $number;
        }
    }

    return null;
}

function getStringOption(array $argv, string $name, bool $required = false): ?string
{
    foreach ($argv as $i => $arg) {
        if ($arg === $name) {
            $value = $argv[$i + 1] ?? null;

            if ($value === null || str_starts_with($value, '--')) {
                fwrite(STDERR, "Ошибка: {$name} ожидает строковое значение.\n");
                exit(1);
            }

            if (mb_strlen($value) < 3) {
                fwrite(STDERR, "Ошибка: {$name} ожидает строку длиной не менее 3 символов.\n");
                exit(1);
            }

            return (string)$value;
        }
    }

    if ($required) {
        fwrite(STDERR, "Ошибка: обязательный параметр {$name} не указан.\n");
        exit(1);
    }

    return null;
}

function printBooksTable(array $hits, string $shop): void
{
    $rows = [];

    foreach ($hits as $hit) {
        $source = $hit['_source'] ?? [];

        $stockForShop = null;
        if (isset($source['stock']) && is_array($source['stock'])) {
            foreach ($source['stock'] as $stockRow) {
                if (($stockRow['shop'] ?? null) === $shop) {
                    $stockForShop = $stockRow['stock'] ?? null;
                    break;
                }
            }
        }

        $rows[] = [
            'title'    => (string)($source['title'] ?? ''),
            'category' => (string)($source['category'] ?? ''),
            'price'    => (string)($source['price'] ?? ''),
            'shop'     => $shop,
            'stock'    => $stockForShop !== null ? (string)$stockForShop : '',
        ];
    }

    if (empty($rows)) {
        echo "Ничего не найдено.\n";
        return;
    }

    $headers = [
        'title'    => 'TITLE',
        'category' => 'CATEGORY',
        'price'    => 'PRICE',
        'shop'     => 'SHOP',
        'stock'    => 'STOCK',
    ];

    $maxLen = 0;
    foreach ($headers as $label) {
        $maxLen = max($maxLen, mb_strlen($label));
    }
    foreach ($rows as $row) {
        foreach ($row as $value) {
            $maxLen = max($maxLen, mb_strlen($value));
        }
    }

    $uniformWidth = min($maxLen, 100);
    $width = 5 + $uniformWidth;

    $separator = '+';
    foreach ($headers as $key => $label) {
        $separator .= str_repeat('-', $width + 2) . '+';
    }

    echo $separator . PHP_EOL;
    echo '|';
    foreach ($headers as $key => $label) {
        echo ' ' . str_pad($label, $width) . ' |';
    }
    echo PHP_EOL;
    echo $separator . PHP_EOL;

    foreach ($rows as $row) {
        echo '|';
        foreach ($headers as $key => $_) {
            $lengthStr = mb_strlen($row[$key]);
            if ($lengthStr > 40){
                $row[$key] = mb_substr($row[$key], 0, 30). '...';
            }

            echo ' '. $row[$key], str_pad('', $width - mb_strlen($row[$key]), ' ') . ' |';
        }
        echo PHP_EOL;
    }

    echo $separator . PHP_EOL;
}

$priceValueMin = getNumericOption($argv, $optionNamePriceMin);
$priceValueMax = getNumericOption($argv, $optionNamePriceMax);
$categoryValue = getStringOption($argv, $optionNameCategory);
$titleValue    = getStringOption($argv, $optionNameTitle, true);
$shopValue     = getStringOption($argv, $optionNameShop, true);

$repository = new BookRepository();
$hits = $repository->getBooksInStock(
    title: $titleValue,
    shop: $shopValue,
    minPrice: $priceValueMin ?? 0,
    maxPrice: $priceValueMax,
    category: $categoryValue,
);

printBooksTable($hits, $shopValue);