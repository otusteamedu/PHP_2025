<?php

declare(strict_types=1);

/**
 * Скрипт проверки порога покрытия кода тестами
 * Парсит PHPUnit clover XML-отчёт и проверяет что line coverage >= минимального порога
 *
 * Использование: php bin/check-coverage.php [clover-file] [min-threshold]
 * По умолчанию: clover.xml, порог 70%
 *
 * Примечание: PHPUnit не поддерживает нативную проверку порога покрытия (в отличие от Pest).
 * Этот скрипт — замена нативной проверки.
 */

$cloverFile = $argv[1] ?? 'coverage.xml';
$minThreshold = (int)($argv[2] ?? 70);

if (!file_exists($cloverFile)) {
    echo "--- Файл покрытия не найден: {$cloverFile}\n";
    echo "Запустите: make test-coverage\n";
    exit(1);
}

$xml = simplexml_load_string(file_get_contents($cloverFile));
if ($xml === false) {
    echo "--- Не удалось распарсить XML: {$cloverFile}\n";
    exit(1);
}

$metrics = $xml->xpath('//metrics')[0] ?? null;
if ($metrics === null) {
    echo "--- Не найдена секция <metrics> в clover-отчёте\n";
    exit(1);
}

$elements = (int)$metrics['elements'];
$coveredElements = (int)$metrics['coveredelements'];
$statements = (int)$metrics['statements'];
$coveredStatements = (int)$metrics['coveredstatements'];
$methods = (int)$metrics['methods'];
$coveredMethods = (int)$metrics['coveredmethods'];
$classes = (int)$metrics['classes'];
$coveredClasses = (int)$metrics['coveredclasses'];

$lineCoverage = $elements > 0 ? round(($coveredElements / $elements) * 100, 2) : 0;
$methodCoverage = $methods > 0 ? round(($coveredMethods / $methods) * 100, 2) : 0;
$classCoverage = $classes > 0 ? round(($coveredClasses / $classes) * 100, 2) : 0;

echo "Покрытие кода тестами:\n";
echo "   Lines:   {$lineCoverage}% ({$coveredElements}/{$elements})\n";
echo "   Methods: {$methodCoverage}% ({$coveredMethods}/{$methods})\n";
echo "   Classes: {$classCoverage}% ({$coveredClasses}/{$classes})\n";
echo "   Порог:   {$minThreshold}%\n\n";

if ($lineCoverage >= $minThreshold) {
    echo "+++ Покрытие {$lineCoverage}% >= {$minThreshold}% — порог пройден\n";
    exit(0);
} else {
    echo "--- Покрытие {$lineCoverage}% < {$minThreshold}% — порог НЕ пройден\n";
    exit(1);
}
