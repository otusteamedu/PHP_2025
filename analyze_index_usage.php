<?php

$host = 'localhost';
$dbname = 'cinema';
$user = 'app';
$password = 'app';
$port = '5432';

$dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
    echo "Успешное подключение к базе данных!\n";
} catch (PDOException $e) {
    exit('Ошибка подключения к БД: '.$e->getMessage());
}

// Запрос для самых часто используемых индексов
$sqlMostUsed = <<<'SQL'
    SELECT
        indexrelname AS index_name,
        relname AS table_name,
        idx_scan AS scan_count
    FROM
        pg_stat_all_indexes
    WHERE
        schemaname = 'public' AND idx_scan > 0
    ORDER BY
        idx_scan DESC
    LIMIT 5;
SQL;

// Запрос для самых редко используемых (включая неиспользуемые) индексов
$sqlLeastUsed = <<<'SQL'
    SELECT
        indexrelname AS index_name,
        relname AS table_name,
        idx_scan AS scan_count
    FROM
        pg_stat_all_indexes
    WHERE
        schemaname = 'public'
    ORDER BY
        idx_scan ASC
    LIMIT 5;
SQL;

try {
    echo "Собираем статистику использования индексов...\n";
    $stmtMost = $pdo->query($sqlMostUsed);
    $mostUsed = $stmtMost->fetchAll();

    $stmtLeast = $pdo->query($sqlLeastUsed);
    $leastUsed = $stmtLeast->fetchAll();

    $report = "Анализ использования индексов в базе данных '{$dbname}'\n\n";

    $report .= "Топ-5 самых ЧАСТО используемых индексов\n";
    $report .= "=========================================\n";
    if (empty($mostUsed)) {
        $report .= "Не найдено использованных индексов.\n";
    } else {
        $report .= str_pad('Индекс', 50).str_pad('Таблица', 30)."Кол-во использований\n";
        $report .= str_pad('------', 50).str_pad('-------', 30)."-----------------------\n";
        foreach ($mostUsed as $row) {
            $report .= str_pad($row['index_name'], 50).str_pad($row['table_name'], 30).$row['scan_count']."\n";
        }
    }
    $report .= "\n\n";

    $report .= "Топ-5 самых РЕДКО используемых (или неиспользуемых) индексов\n";
    $report .= "==============================================================\n";
    if (empty($leastUsed)) {
        $report .= "Не найдено индексов для анализа.\n";
    } else {
        $report .= str_pad('Индекс', 50).str_pad('Таблица', 30)."Кол-во использований\n";
        $report .= str_pad('------', 50).str_pad('-------', 30)."-----------------------\n";
        foreach ($leastUsed as $row) {
            $report .= str_pad($row['index_name'], 50).str_pad($row['table_name'], 30).$row['scan_count']."\n";
        }
    }

    $reportFile = 'index_usage_report.txt';
    file_put_contents($reportFile, $report);

    echo "Анализ завершен. Результаты сохранены в файл: {$reportFile}\n";

} catch (PDOException $e) {
    exit('Ошибка при выполнении запроса: '.$e->getMessage());
}
