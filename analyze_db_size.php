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

$sql = <<<'SQL'
    SELECT
        relname AS object_name,
        pg_size_pretty(pg_total_relation_size(C.oid)) AS total_size,
        CASE relkind
            WHEN 'r' THEN 'Table'
            WHEN 'i' THEN 'Index'
            WHEN 'S' THEN 'Sequence'
            WHEN 'v' THEN 'View'
            WHEN 'm' THEN 'Materialized View'
            WHEN 't' THEN 'TOAST Table'
            ELSE 'Other'
        END AS object_type
    FROM
        pg_class C
    LEFT JOIN
        pg_namespace N ON (N.oid = C.relnamespace)
    WHERE
        nspname NOT IN ('pg_catalog', 'information_schema')
        AND nspname !~ '^pg_toast'
        AND relkind IN ('r', 'i', 'm')
    ORDER BY
        pg_total_relation_size(C.oid) DESC
    LIMIT 15;
SQL;

try {
    echo "Выполняем запрос для анализа размеров объектов БД...\n";
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();

    $report = "Топ-15 самых больших объектов в базе данных '{$dbname}'\n";
    $report .= "===========================================================\n\n";

    if (empty($results)) {
        $report .= "Не удалось найти объекты для анализа.\n";
    } else {
        // Определяем максимальную длину для выравнивания
        $maxLenName = 0;
        $maxLenType = 0;
        foreach ($results as $row) {
            if (strlen($row['object_name']) > $maxLenName) {
                $maxLenName = strlen($row['object_name']);
            }
            if (strlen($row['object_type']) > $maxLenType) {
                $maxLenType = strlen($row['object_type']);
            }
        }

        foreach ($results as $row) {
            $report .=
                str_pad($row['object_name'], $maxLenName + 2).
                str_pad($row['object_type'], $maxLenType + 2).
                $row['total_size']."\n";
        }
    }

    $reportFile = 'db_objects_size_report.txt';
    file_put_contents($reportFile, $report);

    echo "Анализ завершен. Результаты сохранены в файл: {$reportFile}\n";

} catch (PDOException $e) {
    exit('Ошибка при выполнении запроса: '.$e->getMessage());
}
