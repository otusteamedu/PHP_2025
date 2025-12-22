#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Service\ElasticService;

/**
 * Инициализация - создает индекс и загружает данные
 */
echo "Инициализация книжного магазина\n";

// Проверка аргументов
if ($argc < 2) {
    echo "Использование: php init.php <путь_к_файлу_books.json>\n";
    exit(1);
}

$dataFile = $argv[1];

try {
    // Создаем сервис
    $es = new ElasticService();
    
    // Проверяем подключение
    echo "Проверяем подключение к Elasticsearch...\n";
    if (!$es->ping()) {
        echo "Ошибка: Elasticsearch недоступен\n";
        exit(1);
    }
    echo "ОК, Подключение установлено\n";
    
    // Создаем индекс
    echo "Создаем индекс...\n";
    $es->createIndex();
    echo "ОК, Индекс создан\n";
    
    // Загружаем данные
    echo "Загружаем данные из: $dataFile\n";
    $es->importData($dataFile);
    echo "ОК, Данные загружены\n";
    
    echo "\nИнициализация успешно завершена!\n";
    exit(0);
    
} catch (Throwable $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
    exit(1);
}