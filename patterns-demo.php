<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\FactoryMethod\{
    WordDocumentCreator,
    ExcelDocumentCreator,
    PDFDocumentCreator,
    IDocument
};
use App\Adapter\{DocumentFormatAdapter, LegacyDocumentAPI};

// ============================================================
// Демонстрация паттернов проектирования
// ============================================================

echo "ДЕМОНСТРАЦИЯ ПАТТЕРНОВ ПРОЕКТИРОВАНИЯ\n";
echo str_repeat("-", 60) . "\n\n";

// ============================================================
// 1. FACTORY METHOD - создание объектов
// ============================================================

echo "1. FACTORY METHOD\n\n";
echo "Задача: создавать логику для сохранения документов разного типа\n";
echo "Решение: каждый тип документа имеет своего создателя\n\n";

$creators = [
    'Word' => new WordDocumentCreator(),
    'Excel' => new ExcelDocumentCreator(),
    'PDF' => new PDFDocumentCreator(),
];

foreach ($creators as $type => $creator) {
    $tempFile = sys_get_temp_dir() . "/{$type}_doc_" . time() . ".txt";
    $creator->createAndSaveDocument(
        "Содержимое {$type} документа",
        $tempFile
    );
    echo "  ✓ {$type} документ создан\n";
}

echo "\nВсе документы созданы без явного указания конкретных классов.\n";
echo "Клиент работает только с интерфейсом DocumentCreator.\n\n";

// ============================================================
// 2. ADAPTER - преобразование форматов
// ============================================================

echo str_repeat("-", 60) . "\n\n";
echo "2. ADAPTER\n\n";
echo "Задача: преобразовать документ из одного формата в другой\n";
echo "Решение: использут старое API (LegacyDocumentAPI) через адаптер\n\n";

// Создаём исходный документ
$sourceFile = sys_get_temp_dir() . '/source_' . time() . '.txt';
$sourceData = "Строка 1|Данные A\nСтрока 2|Данные B\nСтрока 3|Данные C";
file_put_contents($sourceFile, $sourceData);

echo "Исходный документ создан\n";
echo "Читаем содержимое через LegacyDocumentAPI и преобразуем форматы:\n\n";

$formats = ['excel', 'pdf', 'csv', 'html'];

foreach ($formats as $format) {
    $adapter = new DocumentFormatAdapter(
        new LegacyDocumentAPI(),
        $format
    );
    
    $adapter->open($sourceFile);
    $content = file_get_contents($sourceFile);
    $adapter->save($content);
    $adapter->close();
    
    echo "  ✓ Преобразовано в {$format}\n";
}

echo "\nАдаптер позволяет работать со старым API (LegacyDocumentAPI)\n";
echo "как с новым интерфейсом (IDocument).\n\n";

// ============================================================
// 3. Практическое применение
// ============================================================

echo str_repeat("-", 60) . "\n\n";
echo "3. КАК ЭТО РАБОТАЕТ ВМЕСТЕ\n\n";

$info = [
    'Factory Method' => [
        'применение' => 'создание различных типов объектов',
        'выигрыш' => 'клиент не зависит от конкретных классов',
    ],
    'Adapter' => [
        'применение' => 'интеграция несовместимого кода',
        'выигрыш' => 'переиспользуем старый код без изменений',
    ],
];

foreach ($info as $pattern => $details) {
    echo "{$pattern}:\n";
    foreach ($details as $key => $value) {
        echo "  - {$key}: {$value}\n";
    }
    echo "\n";
}

// ============================================================
// 4. Результаты
// ============================================================

echo str_repeat("-", 60) . "\n\n";
echo "ИТОГОВЫЕ ФАЙЛЫ\n\n";

$tempDir = sys_get_temp_dir();
$files = glob($tempDir . '/*_doc_*.txt');
$adaptedFiles = glob($tempDir . '/*.{csv,pdf,html}', GLOB_BRACE);

if ($files) {
    echo "Документы, созданные Factory Method:\n";
    foreach ($files as $file) {
        echo "  - " . basename($file) . "\n";
    }
    echo "\n";
}

if ($adaptedFiles) {
    echo "Документы, преобразованные Adapter:\n";
    foreach ($adaptedFiles as $file) {
        echo "  - " . basename($file) . "\n";
    }
}

echo "\n" . str_repeat("-", 60) . "\n";
echo "Демонстрация завершена\n";
