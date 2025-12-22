#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Service\ElasticService;

/**
 * Выводит результаты поиска в виде таблицы
 */
function displayResults(array $results): void
{
    if (empty($results['hits']['hits'])) {
        echo "По вашему запросу ничего не найдено\n";
        return;
    }

    $total = $results['hits']['total']['value'];
    
    // Шапка таблицы
    echo str_repeat('=', 90) . "\n";
    printf("| %-40s | %-20s | %-10s | %-8s |\n", 
        'Название', 'Категория', 'Цена, руб.', 'Скор');
    echo str_repeat('=', 90) . "\n";
    
    // Данные
    foreach ($results['hits']['hits'] as $hit) {
        $source = $hit['_source'];
        
        // Обрезаем длинные названия
        $title = mb_strlen($source['title']) > 40 
            ? mb_substr($source['title'], 0, 37) . '...' 
            : $source['title'];
            
        printf("| %-40s | %-20s | %-10d | %-8.2f |\n",
            $title,
            $source['category'],
            $source['price'],
            $hit['_score']);
    }
    
    echo str_repeat('=', 90) . "\n";
    echo "Найдено книг: $total\n";
}

/**
 * Показывает справку по использованию
 */
function printHelp(): void
{
    echo "Поиск по книжному интернет-магазину\n\n";
    echo "Использование:\n";
    echo "  php search.php --query='текст' [опции]\n\n";
    echo "Обязательный параметр:\n";
    echo "  --query='текст'      Поисковый запрос\n\n";
    echo "Опции:\n";
    echo "  --category='кат'     Фильтр по категории\n";
    echo "  --max-price=число    Максимальная цена\n";
    echo "  --no-stock           Показать отсутствующие товары\n";
    echo "  --help               Показать справку\n\n";
    echo "Пример из задания:\n";
    echo "  php search.php --query='рыцОри' --category='Исторический роман' --max-price=2000\n";
}

// Получаем аргументы командной строки
$options = getopt('', ['query:', 'category:', 'max-price:', 'no-stock', 'help']);

// Показываем справку если нужно
if (isset($options['help'])) {
    printHelp();
    exit(0);
}

// Проверяем обязательный параметр
if (!isset($options['query'])) {
    printHelp();
    echo "\nОшибка: параметр --query обязателен\n";
    exit(1);
}

// Получаем параметры
$query = $options['query'];
$category = $options['category'] ?? null;
$maxPrice = isset($options['max-price']) ? (int)$options['max-price'] : null;
$inStock = !isset($options['no-stock']);

try {
    // Выводим информацию о поиске
    echo "Поиск по книжному магазину: \n";
    echo "Запрос: \"$query\"\n";
    if ($category) echo "Категория: $category\n";
    if ($maxPrice) echo "Максимальная цена: $maxPrice руб.\n";
    echo "Только в наличии: " . ($inStock ? 'да' : 'нет') . "\n\n";
    
    // Выполняем поиск
    $es = new ElasticService();
    $results = $es->search($query, $category, $maxPrice, $inStock);
    
    // Выводим результаты
    displayResults($results);
    
    exit(0);
    
} catch (Throwable $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
    exit(1);
}